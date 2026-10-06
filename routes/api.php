// routes/api.php

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================
// PUBLIC ROUTES (Tidak perlu login)
// ============================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Alat (read-only)
Route::get('/alat', [AlatController::class, 'index']);
Route::get('/alat/{alat}', [AlatController::class, 'show']);
Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/kategori/{kategori}/alat', [KategoriController::class, 'alatByKategori']);

// ============================================
// PROTECTED ROUTES (Harus login)
// ============================================

Route::middleware('auth:sanctum')->group(function () {

    // ============================================
    // AUTH
    // ============================================
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ============================================
    // PROFILE
    // ============================================
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
    });

    // ============================================
    // NOTIFICATIONS
    // ============================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [ProfileController::class, 'notifications']);
        Route::put('/{notification}/read', [ProfileController::class, 'markAsRead']);
        Route::put('/read-all', [ProfileController::class, 'markAllAsRead']);
    });

    // ============================================
    // ALAT (CRUD untuk admin)
    // ============================================
    Route::prefix('alat')->group(function () {
        // Admin only
        Route::middleware('admin')->group(function () {
            Route::post('/', [AlatController::class, 'store']);
            Route::put('/{alat}', [AlatController::class, 'update']);
            Route::delete('/{alat}', [AlatController::class, 'destroy']);
            Route::patch('/{alat}/toggle-status', [AlatController::class, 'toggleStatus']);
        });

        // Petugas & Admin
        Route::middleware('petugas')->group(function () {
            Route::get('/available', [AlatController::class, 'available']);
            Route::get('/{alat}/history', [AlatController::class, 'history']);
        });
    });

    // ============================================
    // KATEGORI (Admin only)
    // ============================================
    Route::prefix('kategori')->middleware('admin')->group(function () {
        Route::post('/', [KategoriController::class, 'store']);
        Route::put('/{kategori}', [KategoriController::class, 'update']);
        Route::delete('/{kategori}', [KategoriController::class, 'destroy']);
        Route::patch('/{kategori}/toggle-status', [KategoriController::class, 'toggleStatus']);
    });

    // ============================================
    // PEMINJAMAN
    // ============================================
    Route::prefix('peminjaman')->group(function () {

        // User Routes
        Route::get('/user', [PeminjamanController::class, 'userPeminjaman']);
        Route::post('/', [PeminjamanController::class, 'store']);
        Route::get('/{peminjaman}', [PeminjamanController::class, 'show']);
        Route::post('/{peminjaman}/batalkan', [PeminjamanController::class, 'batalkan']);
        Route::get('/check-availability', [PeminjamanController::class, 'checkAvailability']);

        // Petugas & Admin Routes
        Route::middleware('petugas')->group(function () {
            Route::get('/', [PeminjamanController::class, 'index']);
            Route::post('/{peminjaman}/verifikasi', [PeminjamanController::class, 'verifikasi']);
            Route::post('/{peminjaman}/ambil', [PeminjamanController::class, 'ambil']);
            Route::post('/{peminjaman}/batalkan-petugas', [PeminjamanController::class, 'batalkanPetugas']);
            Route::get('/status/count', [PeminjamanController::class, 'statusCount']);
        });

        // Admin only
        Route::middleware('admin')->group(function () {
            Route::delete('/{peminjaman}', [PeminjamanController::class, 'destroy']);
        });
    });

    // ============================================
    // PENGEMBALIAN
    // ============================================
    Route::prefix('pengembalian')->middleware('petugas')->group(function () {
        Route::get('/', [PengembalianController::class, 'index']);
        Route::get('/{peminjaman}/proses', [PengembalianController::class, 'proses']);
        Route::post('/{peminjaman}', [PengembalianController::class, 'store']);
        Route::get('/{pengembalian}', [PengembalianController::class, 'show']);
        Route::get('/{pengembalian}/receipt', [PengembalianController::class, 'receipt']);
    });

    // ============================================
    // LAPORAN (Admin & Petugas)
    // ============================================
    Route::prefix('laporan')->middleware('petugas')->group(function () {
        // Admin & Petugas
        Route::get('/dashboard', [LaporanController::class, 'dashboard']);
        Route::get('/peminjaman', [LaporanController::class, 'peminjaman']);
        Route::get('/pengembalian', [LaporanController::class, 'pengembalian']);
        Route::get('/alat-terpopuler', [LaporanController::class, 'alatTerpopuler']);
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf']);
        Route::get('/export-excel', [LaporanController::class, 'exportExcel']);

        // Admin only
        Route::middleware('admin')->group(function () {
            Route::get('/users', [LaporanController::class, 'users']);
            Route::get('/logs', [LaporanController::class, 'logs']);
            Route::get('/denda', [LaporanController::class, 'denda']);
        });
    });

    // ============================================
    // ADMIN ONLY ROUTES
    // ============================================
    Route::prefix('admin')->middleware('admin')->group(function () {

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [ProfileController::class, 'index']);
            Route::post('/', [ProfileController::class, 'store']);
            Route::get('/{user}', [ProfileController::class, 'show']);
            Route::put('/{user}', [ProfileController::class, 'update']);
            Route::delete('/{user}', [ProfileController::class, 'destroy']);
            Route::patch('/{user}/toggle-status', [ProfileController::class, 'toggleStatus']);
        });

        // System Settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [ProfileController::class, 'settings']);
            Route::put('/', [ProfileController::class, 'updateSettings']);
        });

        // Denda Management
        Route::prefix('denda')->group(function () {
            Route::get('/', [LaporanController::class, 'denda']);
            Route::put('/{denda}/bayar', [LaporanController::class, 'bayarDenda']);
        });
    });
});

// ============================================
// FALLBACK ROUTE (404)
// ============================================
Route::fallback(function () {
    return response()->json([
        'status' => false,
        'message' => 'Endpoint not found'
    ], 404);
});