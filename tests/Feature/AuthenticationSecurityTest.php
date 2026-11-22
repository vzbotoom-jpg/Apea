<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    public function test_inactive_user_is_logged_out_and_denied_web_access(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_change_invalidates_an_existing_web_session(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        Route::middleware('web')->get(
            '/_test/authenticated-session',
            fn () => response()->noContent(),
        );

        $firstResponse = $this->actingAs($user)->get('/_test/authenticated-session');
        self::assertSame(204, $firstResponse->getStatusCode(), $firstResponse->getContent());

        $user->forceFill(['password' => Hash::make('new-password')])->save();

        $response = $this->get('/_test/authenticated-session');

        self::assertSame(302, $response->getStatusCode(), $response->getContent());
        self::assertSame(route('login'), $response->headers->get('Location'));
        $this->assertGuest();
    }

    public function test_api_registration_issues_a_working_sanctum_token(): void
    {
        Notification::fake();
        $response = $this->postJson('/api/register', [
            'name' => 'API User',
            'email' => 'api-user@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'api-user@example.test')
            ->assertJsonPath('data.token_type', 'Bearer');

        $user = User::where('email', 'api-user@example.test')->firstOrFail();
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->withToken($response->json('data.token'))
            ->getJson('/api/user')
            ->assertForbidden();

        $user->forceFill(['email_verified_at' => now()])->save();
        $this->app['auth']->forgetGuards();

        $this->withToken($response->json('data.token'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', 'api-user@example.test');
    }

    public function test_web_registration_sends_verification_and_redirects_to_notice(): void
    {
        Notification::fake();

        $this->post(route('register'), [
            'name' => 'Web User',
            'email' => 'web-user@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'web-user@example.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get(route('user.dashboard'))->assertRedirect(route('verification.notice'));
    }

    public function test_verified_link_can_be_opened_without_a_web_session(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'user']);
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->get($verificationUrl)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Email berhasil diverifikasi. Silakan login.');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_inactive_user_cannot_use_an_api_token(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertForbidden()
            ->assertJsonPath('status', false);
    }

    public function test_password_reset_requests_are_throttled_by_ip_and_email(): void
    {
        $email = 'reset-limit@example.test';

        for ($request = 0; $request < 5; $request++) {
            $this->from(route('password.request'))
                ->post(route('password.email'), ['email' => $email])
                ->assertRedirect(route('password.request'));
        }

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $email])
            ->assertTooManyRequests();
    }

    public function test_password_reset_response_does_not_disclose_whether_an_email_exists(): void
    {
        Notification::fake();
        $message = 'Jika alamat email terdaftar, tautan reset password akan dikirim.';
        User::factory()->create(['email' => 'known-reset@example.test']);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'known-reset@example.test'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('success', $message)
            ->assertSessionMissing('errors');

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown-reset@example.test'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('success', $message)
            ->assertSessionMissing('errors');
    }
}
