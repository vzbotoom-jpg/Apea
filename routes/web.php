<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AlatController as AdminAlatController;
use App\Http\Controllers\Admin\KategoriController as AdminKategoriController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PeminjamanController as AdminPeminjamanController;
use App\Http\Controllers\Admin\PengembalianController as AdminPengembalianController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\LogActivityController as AdminLogActivityController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\AlatController as PetugasAlatController;
use App\Http\Controllers\Petugas\PeminjamanController as PetugasPeminjamanController;
use App\Http\Controllers\Petugas\PengembalianController as PetugasPengembalianController;

use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\AlatController as UserAlatController;
use App\Http\Controllers\User\PeminjamanController as UserPeminjamanController;
use App\Http\Controllers\User\ProfileController as UserProfileController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\PembayaranController;

use Illuminate\Support\Facades\Route;

// ============================================
// HOME & PUBLIC PAGES
// ============================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/overview', [HomeController::class, 'overview'])->name('overview');

// ============================================
// PUBLIC INFORMATION PAGES (Baru)
// ============================================
Route::get('/alat', [HomeController::class, 'alat'])->name('alat');
Route::get('/guide', [HomeController::class, 'guide'])->name('guide');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/usage', [HomeController::class, 'usage'])->name('usage');
Route::get('/privacy-choices', [HomeController::class, 'privacyChoices'])->name('privacy-choices');

// ============================================
// AUTH ROUTES (Custom)
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/model', [HomeController::class, 'model'])->name('model');

    // ✅ BARU: Forgot Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/qr/alat/{alat}', [QrController::class, 'alat'])->name('qr.alat');
    Route::get('/qr/alat/{alat}/print', [QrController::class, 'print'])->name('qr.alat.print');
    Route::get('/qr/peminjaman/{peminjaman}', [QrController::class, 'peminjaman'])->name('qr.peminjaman');
});

// ===== USER: kirim pembayaran =====
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('peminjaman/{peminjaman}/bayar', [PembayaranController::class, 'form'])->name('user.peminjaman.bayar');
    Route::post('peminjaman/{peminjaman}/bayar', [PembayaranController::class, 'store'])->name('user.peminjaman.bayar.store');
});

// ===== PETUGAS & ADMIN: verifikasi pembayaran =====
Route::prefix('pembayaran')->name('pembayaran.')->middleware('auth')->group(function () {
    Route::get('/', [PembayaranController::class, 'index'])->name('index');
    Route::get('{pembayaran}', [PembayaranController::class, 'show'])->name('show');
    Route::post('{pembayaran}/setujui', [PembayaranController::class, 'setujui'])->name('setujui');
    Route::post('{pembayaran}/tolak', [PembayaranController::class, 'tolak'])->name('tolak');
});

// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Manajemen Kategori
    Route::resource('kategori', AdminKategoriController::class);
    Route::post('kategori/{kategori}/toggle-status', [AdminKategoriController::class, 'toggleStatus'])->name('kategori.toggle-status');
    
    // Manajemen Alat
    Route::resource('alat', AdminAlatController::class);
    Route::post('alat/{alat}/toggle-status', [AdminAlatController::class, 'toggleStatus'])->name('alat.toggle-status');
    
    // Manajemen User
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Manajemen Peminjaman
    Route::get('peminjaman', [AdminPeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/{peminjaman}', [AdminPeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('peminjaman/{peminjaman}/verifikasi', [AdminPeminjamanController::class, 'verifikasi'])->name('peminjaman.verifikasi');
    Route::post('peminjaman/{peminjaman}/batalkan', [AdminPeminjamanController::class, 'batalkan'])->name('peminjaman.batalkan');
    
    // Manajemen Pengembalian
    Route::get('pengembalian', [AdminPengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('pengembalian/{peminjaman}/proses', [AdminPengembalianController::class, 'proses'])->name('pengembalian.proses');
    Route::post('pengembalian/{peminjaman}/save', [AdminPengembalianController::class, 'save'])->name('pengembalian.save');
    
    // Laporan
    Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/peminjaman', [AdminLaporanController::class, 'peminjaman'])->name('laporan.peminjaman');
    Route::get('laporan/alat-terpopuler', [AdminLaporanController::class, 'alatTerpopuler'])->name('laporan.alat-terpopuler');
    Route::get('laporan/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    
    // Log Activity
    Route::get('logs', [AdminLogActivityController::class, 'index'])->name('logs.index');
    Route::post('logs/clear', [AdminLogActivityController::class, 'clear'])->name('logs.clear');

    // Profile & Settings
    Route::get('profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::get('settings', [AdminProfileController::class, 'settings'])->name('settings');
    Route::post('settings', [AdminProfileController::class, 'updateSettings'])->name('settings.update');
});

// ============================================
// PETUGAS ROUTES
// ============================================
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'petugas'])->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
    
    // Alat
    Route::get('alat', [PetugasAlatController::class, 'index'])->name('alat.index');
    Route::get('alat/{alat}', [PetugasAlatController::class, 'show'])->name('alat.show');
    Route::get('alat/available', [PetugasAlatController::class, 'getAvailableAlats'])->name('alat.available');

    Route::get('scan', [PetugasAlatController::class, 'scan'])->name('alat.scan');
    
    // Peminjaman
    Route::get('peminjaman', [PetugasPeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/{peminjaman}', [PetugasPeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('peminjaman/{peminjaman}/verifikasi', [PetugasPeminjamanController::class, 'verifikasi'])->name('peminjaman.verifikasi');
    Route::post('peminjaman/{peminjaman}/ambil', [PetugasPeminjamanController::class, 'ambil'])->name('peminjaman.ambil');
    Route::post('peminjaman/{peminjaman}/batalkan', [PetugasPeminjamanController::class, 'batalkan'])->name('peminjaman.batalkan');
    Route::post('peminjaman/{peminjaman}/konfirmasi-bayar', [PetugasPeminjamanController::class, 'konfirmasiBayar'])
        ->name('peminjaman.konfirmasi-bayar');
    Route::post('peminjaman/{peminjaman}/perpanjang/setujui', [PetugasPeminjamanController::class, 'setujuiPerpanjangan'])
        ->name('peminjaman.perpanjangan.setujui');
    Route::post('peminjaman/{peminjaman}/perpanjang/tolak', [PetugasPeminjamanController::class, 'tolakPerpanjangan'])
        ->name('peminjaman.perpanjangan.tolak');
    
    // Pengembalian
    Route::get('pengembalian', [PetugasPengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('pengembalian/{peminjaman}/proses', [PetugasPengembalianController::class, 'proses'])->name('pengembalian.proses');
    Route::post('pengembalian/{peminjaman}/save', [PetugasPengembalianController::class, 'save'])->name('pengembalian.save');
    Route::get('pengembalian/report', [PetugasPengembalianController::class, 'report'])->name('pengembalian.report');
    Route::get('pengembalian/{pengembalian}/receipt', [PetugasPengembalianController::class, 'printReceipt'])->name('pengembalian.receipt');
});

// ============================================
// USER ROUTES
// ============================================
Route::prefix('user')->name('user.')->middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // Alat
    Route::get('alat', [UserAlatController::class, 'index'])->name('alat.index');
    Route::get('alat/{alat}', [UserAlatController::class, 'show'])->name('alat.show');
    Route::get('alat/cari', [UserAlatController::class, 'cari'])->name('alat.cari');
    
    // Peminjaman
    Route::get('peminjaman', [UserPeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [UserPeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [UserPeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('peminjaman/{peminjaman}', [UserPeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('peminjaman/{peminjaman}/batalkan', [UserPeminjamanController::class, 'batalkan'])->name('peminjaman.batalkan');
    Route::post('peminjaman/{peminjaman}/perpanjang', [UserPeminjamanController::class, 'requestPerpanjangan'])
        ->name('peminjaman.perpanjang');
    
    // Profile
    Route::get('profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [UserProfileController::class, 'update'])->name('profile.update');
});