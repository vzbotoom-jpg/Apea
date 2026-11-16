<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $response = $this->postJson('/api/register', [
            'name' => 'API User',
            'email' => 'api-user@example.test',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'api-user@example.test')
            ->assertJsonPath('data.token_type', 'Bearer');

        $this->withToken($response->json('data.token'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', 'api-user@example.test');
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
}
