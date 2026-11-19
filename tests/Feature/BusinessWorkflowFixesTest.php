<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Denda;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Setting;
use App\Models\User;
use App\Services\DendaService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessWorkflowFixesTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_web_and_api_loans_snapshot_rent_for_the_full_duration(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(3);
        $start = now()->addDay()->toDateString();
        $due = now()->addDays(4)->toDateString();

        $this->actingAs($user)->post(route('user.peminjaman.store'), [
            'alat_ids' => [$alat->id],
            'tanggal_pinjam' => $start,
            'tanggal_jatuh_tempo' => $due,
        ])->assertRedirect();

        $webDetail = DetailPeminjaman::firstOrFail();
        $this->assertSame(30000, $webDetail->subtotal);
        $this->assertSame(5000, $webDetail->denda_per_hari_saat_pinjam);

        $apiUser = User::factory()->create(['role' => 'user']);
        $apiToken = $apiUser->createToken('test')->plainTextToken;

        $this->withToken($apiToken)->postJson('/api/peminjaman', [
            'alat_ids' => [$alat->fresh()->id],
            'tanggal_pinjam' => $start,
            'tanggal_jatuh_tempo' => $due,
        ])->assertCreated();

        $apiDetail = DetailPeminjaman::latest('id')->firstOrFail();
        $this->assertSame(30000, $apiDetail->subtotal);
        $this->assertSame(5000, $apiDetail->denda_per_hari_saat_pinjam);
    }

    public function test_user_can_upload_a_profile_photo_and_replaces_the_previous_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('foto-profil/old.jpg', 'old photo');
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'foto_profil' => 'foto-profil/old.jpg',
            'role' => 'user',
        ]);

        $this->actingAs($user)->put(route('user.profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'foto_profil' => UploadedFile::fake()->image('profile.jpg'),
        ])->assertRedirect(route('user.profile.edit'));

        $photoPath = $user->fresh()->foto_profil;
        $this->assertNotSame('foto-profil/old.jpg', $photoPath);
        Storage::disk('public')->assertExists($photoPath);
        Storage::disk('public')->assertMissing('foto-profil/old.jpg');

        $this->get(route('user.profile.edit'))
            ->assertOk()
            ->assertSee('Foto Profil')
            ->assertSee(asset('storage/'.$photoPath));
    }

    public function test_late_fee_uses_snapshotted_per_item_rate_and_quantity(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        Setting::set('masa_tenggang_hari', 2);

        $user = User::factory()->create();
        $first = $this->createAlat(5, ['denda_per_hari' => 2500]);
        $second = $this->createAlat(5, ['denda_per_hari' => 4000]);
        $loan = $this->createLoan($user, $first, [
            'status' => 'terlambat',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-12',
        ]);
        $this->createDetail($loan, $first, 2, 10000, 2500);
        $this->createDetail($loan, $second, 1, 15000, 4000);

        $this->assertSame(27000, $loan->calculateDenda());
        $this->assertSame(27000, $loan->total_bayar - $loan->detailPeminjamans->sum('subtotal'));
    }

    public function test_admin_return_marks_lost_units_out_of_stock_and_keeps_fines_due(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        Setting::set('masa_tenggang_hari', 1);
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(2, ['stok_tersedia' => 1, 'denda_per_hari' => 2000]);
        $loan = $this->createLoan($borrower, $alat, [
            'status' => 'terlambat',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-14',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 2000);

        $this->actingAs($admin)->post(route('admin.pengembalian.save', $loan), [
            'kondisi_alat' => 'hilang',
            'denda' => 0,
        ])->assertRedirect(route('admin.pengembalian.index'));

        $this->assertSame(1, $alat->fresh()->stok);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertSame('dikembalikan', $loan->fresh()->status);
        $this->assertSame(4000, $loan->fresh()->total_bayar - 10000);
        $this->assertSame('belum_bayar', $loan->fresh()->status_pembayaran);
        $this->assertSame('belum_bayar', Denda::where('peminjaman_id', $loan->id)->value('status'));
    }

    public function test_api_return_uses_the_automatic_fine_instead_of_client_supplied_amount(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        Setting::set('masa_tenggang_hari', 0);
        $staff = User::factory()->create(['role' => 'petugas']);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1, ['denda_per_hari' => 3000]);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dipinjam',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-16',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 3000);
        $token = $staff->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson("/api/pengembalian/{$loan->id}", [
            'kondisi_alat' => 'baik',
            'denda' => 0,
        ])->assertCreated()
            ->assertJsonPath('data.denda', 3000);

        $this->assertSame(3000, Denda::where('peminjaman_id', $loan->id)->value('jumlah_denda'));
    }

    public function test_payment_after_return_uses_only_the_outstanding_balance_and_pays_the_fine(): void
    {
        $staff = User::factory()->create(['role' => 'petugas']);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dikembalikan',
            'status_pembayaran' => 'belum_lunas',
        ]);
        $this->createDetail($loan, $alat, 1, 20000, 5000);
        $fine = Denda::create([
            'peminjaman_id' => $loan->id,
            'jumlah_denda' => 5000,
            'tanggal_denda' => now(),
            'hari_terlambat' => 1,
            'status' => 'belum_bayar',
        ]);
        Pembayaran::create([
            'peminjaman_id' => $loan->id,
            'user_id' => $user->id,
            'metode' => 'tunai',
            'nominal' => 20000,
            'status' => 'disetujui',
        ]);

        $this->assertSame(25000, $loan->fresh()->total_bayar);
        $this->assertSame(5000, $loan->fresh()->getSisaTagihan());

        $payment = Pembayaran::create([
            'peminjaman_id' => $loan->id,
            'user_id' => $user->id,
            'metode' => 'tunai',
            'nominal' => 5000,
            'status' => 'menunggu',
        ]);

        $this->actingAs($staff)->post(route('pembayaran.setujui', $payment))
            ->assertRedirect(route('pembayaran.index'));

        $this->assertSame('lunas', $fine->fresh()->status);
        $this->assertSame('lunas', $loan->fresh()->status_pembayaran);
        $this->assertSame(0, $loan->fresh()->getSisaTagihan());
    }

    public function test_admin_api_fine_confirmation_records_a_payment_and_reduces_the_balance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dikembalikan',
            'status_pembayaran' => 'belum_lunas',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);
        $fine = Denda::create([
            'peminjaman_id' => $loan->id,
            'jumlah_denda' => 5000,
            'tanggal_denda' => now(),
            'hari_terlambat' => 1,
            'status' => 'belum_bayar',
        ]);
        $token = $admin->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson("/api/admin/denda/{$fine->id}/bayar", [
            'metode' => 'tunai',
        ])->assertOk();

        $this->assertSame('lunas', $fine->fresh()->status);
        $this->assertSame(5000, Pembayaran::where('peminjaman_id', $loan->id)->sum('nominal'));
        $this->assertSame(10000, $loan->fresh()->getSisaTagihan());
        $this->assertSame('belum_bayar', $loan->fresh()->status_pembayaran);
    }

    public function test_payment_form_creates_an_instruction_without_nominal_or_proof_input(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('qr-pembayaran/transfer-bca.png', 'fake-qr');
        Setting::set('metode_pembayaran_aktif', json_encode(['transfer_bca']));
        Setting::set('qr_pembayaran', json_encode(['transfer_bca' => 'qr-pembayaran/transfer-bca.png']));
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dikembalikan',
            'status_pembayaran' => 'belum_lunas',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);
        Pembayaran::create([
            'peminjaman_id' => $loan->id,
            'user_id' => $user->id,
            'metode' => 'tunai',
            'nominal' => 10000,
            'status' => 'disetujui',
        ]);
        Denda::create([
            'peminjaman_id' => $loan->id,
            'jumlah_denda' => 5000,
            'tanggal_denda' => now(),
            'hari_terlambat' => 1,
            'status' => 'belum_bayar',
        ]);

        $this->actingAs($user)->get(route('user.peminjaman.bayar', $loan))
            ->assertOk()
            ->assertSee('Transfer Bank BCA')
            ->assertDontSee('name="nominal"', false)
            ->assertDontSee('name="bukti"', false);

        $this->actingAs($user)->post(route('user.peminjaman.bayar.store', $loan), [
            'metode' => 'qris',
        ])->assertSessionHasErrors('metode');

        $response = $this->actingAs($user)->post(route('user.peminjaman.bayar.store', $loan), [
            'metode' => 'transfer_bca',
        ])->assertRedirect();
        $payment = Pembayaran::where('peminjaman_id', $loan->id)->where('status', 'menunggu')->firstOrFail();

        $this->assertSame(5000, $payment->nominal);
        $this->assertNull($payment->bukti_path);
        $this->assertSame('qr-pembayaran/transfer-bca.png', $payment->qr_path);
        $this->assertSame(route('user.pembayaran.instruksi', $payment), $response->headers->get('Location'));
        $this->actingAs($user)->get(route('user.pembayaran.instruksi', $payment))
            ->assertOk()
            ->assertSee('Scan QR Transfer Bank BCA')
            ->assertSee('Rp 5.000')
            ->assertSee('storage/qr-pembayaran/transfer-bca.png');
        $this->actingAs($user)->get(route('user.peminjaman.bayar', $loan))
            ->assertRedirect(route('user.pembayaran.instruksi', $payment));

        $otherUser = User::factory()->create(['role' => 'user']);
        $this->actingAs($otherUser)
            ->get(route('user.pembayaran.instruksi', $payment))
            ->assertForbidden();

        $payment->update(['status' => 'disetujui']);
        $loan->update(['status_pembayaran' => 'lunas']);
        $this->actingAs($user)->post(route('user.peminjaman.bayar.store', $loan), [
            'metode' => 'transfer_bca',
        ])->assertStatus(422);
    }

    public function test_admin_can_enable_payment_methods_and_upload_qr_codes(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'max_alat_per_hari' => 2,
            'masa_tenggang_hari' => 2,
            'denda_per_hari' => 5000,
            'maks_perpanjangan_hari' => 7,
            'metode_pembayaran_aktif' => ['tunai', 'transfer_bri', 'qris'],
            'qr_codes' => [
                'transfer_bri' => UploadedFile::fake()->image('bri.png'),
                'qris' => UploadedFile::fake()->image('qris.png'),
            ],
        ])->assertRedirect(route('admin.settings'));

        $methods = json_decode(Setting::get('metode_pembayaran_aktif'), true);
        $qrCodes = json_decode(Setting::get('qr_pembayaran'), true);

        $this->assertSame(['tunai', 'transfer_bri', 'qris'], $methods);
        Storage::disk('public')->assertExists($qrCodes['transfer_bri']);
        Storage::disk('public')->assertExists($qrCodes['qris']);
        $this->assertSame(['tunai', 'transfer_bri', 'qris'], Pembayaran::enabledMethods());
        $this->actingAs($admin)->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Transfer Bank BRI')
            ->assertSee($qrCodes['qris']);
    }

    public function test_admin_cannot_enable_a_non_cash_method_without_its_qr_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.settings.update'), [
            'max_alat_per_hari' => 2,
            'masa_tenggang_hari' => 2,
            'denda_per_hari' => 5000,
            'maks_perpanjangan_hari' => 7,
            'metode_pembayaran_aktif' => ['tunai', 'qris'],
        ])->assertSessionHasErrors('qr_codes.qris');
    }

    public function test_user_can_open_payment_form_after_a_loan_is_verified(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'diverifikasi',
            'status_pembayaran' => 'belum_lunas',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);

        $this->actingAs($user)->get(route('user.peminjaman.bayar', $loan))
            ->assertOk();

        $this->actingAs($user)->post(route('user.peminjaman.bayar.store', $loan), [
            'metode' => 'tunai',
        ])->assertRedirect();

        $payment = Pembayaran::where('peminjaman_id', $loan->id)->firstOrFail();
        $this->actingAs($user)->get(route('user.pembayaran.instruksi', $payment))
            ->assertOk()
            ->assertSee('Bayar di loket petugas')
            ->assertDontSee('QR tujuan pembayaran');
    }

    public function test_stock_cannot_be_reduced_below_units_unavailable_for_borrowing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $alat = $this->createAlat(2, ['stok_tersedia' => 0, 'status' => 'dipinjam']);
        $data = [
            'nama_alat' => $alat->nama_alat,
            'kategori_id' => $alat->kategori_id,
            'stok' => 1,
            'kondisi' => 'baik',
            'status' => 'dipinjam',
            'harga_sewa_per_hari' => $alat->harga_sewa_per_hari,
            'denda_per_hari' => $alat->denda_per_hari,
        ];

        $this->actingAs($admin)->put(route('admin.alat.update', $alat), $data)
            ->assertSessionHasErrors('stok');
        $this->assertSame(2, $alat->fresh()->stok);

        $token = $admin->createToken('test')->plainTextToken;
        $this->withToken($token)->putJson("/api/alat/{$alat->id}", $data)
            ->assertUnprocessable();
        $this->assertSame(2, $alat->fresh()->stok);
    }

    public function test_extension_uses_configured_limit_and_cannot_be_approved_after_return(): void
    {
        Setting::set('maks_perpanjangan_hari', 10);
        $user = User::factory()->create(['role' => 'user']);
        $staff = User::factory()->create(['role' => 'petugas']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dipinjam',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($user)->post(route('user.peminjaman.perpanjang', $loan), [
            'tambahan_hari' => 10,
            'alasan' => 'Kegiatan belum selesai',
        ])->assertRedirect();

        $loan->refresh();
        $this->assertSame(now()->addDays(13)->toDateString(), $loan->tanggal_perpanjangan->toDateString());

        $loan->update(['status' => 'dikembalikan']);
        $this->actingAs($staff)->post(route('petugas.peminjaman.perpanjangan.setujui', $loan))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('menunggu', $loan->fresh()->status_perpanjangan);
    }

    public function test_reminder_command_uses_the_application_notification_model(): void
    {
        Carbon::setTestNow('2026-11-17 07:00:00');
        Setting::set('masa_tenggang_hari', 0);
        $user = User::factory()->create();
        $staff = User::factory()->create(['role' => 'petugas']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'dipinjam',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-16',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);

        $this->artisan('peminjaman:ingatkan')->assertSuccessful();

        $this->assertSame('terlambat', $loan->fresh()->status);
        $this->assertSame(1, Notifikasi::where('user_id', $user->id)->count());
        $this->assertSame(5000, Denda::where('peminjaman_id', $loan->id)->value('jumlah_denda'));
        $this->assertSame('belum_bayar', Denda::where('peminjaman_id', $loan->id)->value('status'));
        $this->actingAs($staff)->get(route('denda.index'))
            ->assertOk()
            ->assertSee($loan->kode_peminjaman)
            ->assertSee('Rp 5.000');

        Carbon::setTestNow('2026-11-18 07:00:00');
        $this->artisan('peminjaman:ingatkan')->assertSuccessful();
        $this->assertSame(1, Denda::where('peminjaman_id', $loan->id)->count());
        $this->assertSame(10000, Denda::where('peminjaman_id', $loan->id)->value('jumlah_denda'));
        $this->assertSame(2, Denda::where('peminjaman_id', $loan->id)->value('hari_terlambat'));
    }

    public function test_fine_paid_before_return_stays_paid_and_is_not_duplicated(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        Setting::set('masa_tenggang_hari', 0);
        $user = User::factory()->create(['role' => 'user']);
        $staff = User::factory()->create(['role' => 'petugas']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'terlambat',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-16',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);
        app(DendaService::class)->synchronizeLoan($loan->id);

        $this->actingAs($user)->post(route('user.peminjaman.bayar.store', $loan), [
            'metode' => 'tunai',
        ])->assertRedirect();
        $payment = Pembayaran::where('peminjaman_id', $loan->id)->firstOrFail();
        $this->actingAs($staff)->post(route('pembayaran.setujui', $payment))
            ->assertRedirect(route('pembayaran.index'));

        $this->assertSame('lunas', Denda::where('peminjaman_id', $loan->id)->value('status'));
        $this->actingAs($staff)->post(route('petugas.pengembalian.save', $loan), [
            'kondisi_alat' => 'baik',
        ])->assertRedirect(route('petugas.pengembalian.index'));

        $this->assertSame(1, Denda::where('peminjaman_id', $loan->id)->count());
        $this->assertSame('lunas', Denda::where('peminjaman_id', $loan->id)->value('status'));
        $this->assertSame(5000, $loan->fresh()->pengembalian->denda);
    }

    public function test_legacy_paid_status_does_not_cover_fines_accrued_afterward(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        Setting::set('masa_tenggang_hari', 0);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'terlambat',
            'status_pembayaran' => 'lunas',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-16',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);

        app(DendaService::class)->synchronizeLoan($loan->id);

        $this->assertSame('belum_bayar', Denda::where('peminjaman_id', $loan->id)->value('status'));
        $this->assertSame(5000, $loan->fresh()->getSisaTagihan());
    }

    public function test_increased_fine_preserves_legacy_paid_amount_as_a_payment_record(): void
    {
        Carbon::setTestNow('2026-11-18 07:00:00');
        Setting::set('masa_tenggang_hari', 0);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat, [
            'status' => 'terlambat',
            'status_pembayaran' => 'lunas',
            'tanggal_pinjam' => '2026-11-01',
            'tanggal_jatuh_tempo' => '2026-11-16',
        ]);
        $this->createDetail($loan, $alat, 1, 10000, 5000);
        Denda::create([
            'peminjaman_id' => $loan->id,
            'jumlah_denda' => 5000,
            'tanggal_denda' => '2026-11-17',
            'hari_terlambat' => 1,
            'status' => 'lunas',
        ]);

        app(DendaService::class)->synchronizeLoan($loan->id);

        $this->assertSame(10000, Denda::where('peminjaman_id', $loan->id)->value('jumlah_denda'));
        $this->assertSame('belum_bayar', Denda::where('peminjaman_id', $loan->id)->value('status'));
        $this->assertSame(15000, Pembayaran::where('peminjaman_id', $loan->id)->sum('nominal'));
        $this->assertSame(5000, $loan->fresh()->getSisaTagihan());
    }

    public function test_user_with_loan_history_cannot_be_deleted_and_has_no_unimplemented_show_route(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $loan = $this->createLoan($user, $alat);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertFalse(Route::has('admin.users.show'));

        $token = $admin->createToken('test')->plainTextToken;
        $this->withToken($token)->deleteJson("/api/admin/users/{$user->id}")
            ->assertStatus(409);
        $this->assertDatabaseHas('peminjamans', ['id' => $loan->id]);
    }

    public function test_monthly_report_excludes_same_month_from_previous_year(): void
    {
        Carbon::setTestNow('2026-11-17 12:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);
        $previousYear = $this->createLoan($user, $alat);
        $previousYear->forceFill(['created_at' => '2025-11-10 10:00:00'])->save();
        $thisYear = $this->createLoan($user, $alat);
        $thisYear->forceFill(['created_at' => '2026-11-10 10:00:00'])->save();

        $this->actingAs($admin)->get(route('admin.laporan.index'))
            ->assertOk()
            ->assertViewHas('peminjamanBulanIni', 1);
    }

    private function createAlat(int $stock, array $attributes = []): Alat
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kategori '.uniqid(),
            'slug' => 'kategori-'.uniqid(),
        ]);

        return Alat::create(array_merge([
            'kode_alat' => 'ALT-'.uniqid(),
            'nama_alat' => 'Alat '.uniqid(),
            'slug' => 'alat-'.uniqid(),
            'kategori_id' => $kategori->id,
            'stok' => $stock,
            'stok_tersedia' => $stock,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 10000,
            'denda_per_hari' => 5000,
        ], $attributes));
    }

    private function createLoan(User $user, Alat $alat, array $attributes = []): Peminjaman
    {
        return Peminjaman::create(array_merge([
            'kode_peminjaman' => 'PJM-'.uniqid(),
            'user_id' => $user->id,
            'tanggal_pinjam' => now()->subDay()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDay()->toDateString(),
            'status' => 'menunggu',
            'status_pembayaran' => 'belum_lunas',
        ], $attributes));
    }

    private function createDetail(
        Peminjaman $loan,
        Alat $alat,
        int $quantity,
        int $subtotal,
        ?int $dailyFine = null
    ): DetailPeminjaman {
        return DetailPeminjaman::create([
            'peminjaman_id' => $loan->id,
            'alat_id' => $alat->id,
            'jumlah' => $quantity,
            'harga_sewa_saat_pinjam' => 10000,
            'denda_per_hari_saat_pinjam' => $dailyFine,
            'subtotal' => $subtotal,
        ]);
    }
}
