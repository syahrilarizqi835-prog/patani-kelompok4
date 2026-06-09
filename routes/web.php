<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;

// ==========================
// Admin Controllers
// ==========================
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PetaniController;
use App\Http\Controllers\Admin\SawahController as AdminSawahController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Admin\ForumController as AdminForumController;
use App\Http\Controllers\Admin\TransaksiAdminController;

// ==========================
// Petani Controllers
// ==========================
use App\Http\Controllers\Petani\SawahController;
use App\Http\Controllers\Petani\PrediksiController;
use App\Http\Controllers\Petani\PerawatanController;
use App\Http\Controllers\Petani\CuacaController;
use App\Http\Controllers\Petani\ChatbotController;
use App\Http\Controllers\Petani\RiwayatController;
use App\Http\Controllers\Petani\ForumController;
use App\Http\Controllers\Petani\TransaksiController;
use App\Http\Controllers\Petani\NotifikasiController;
use App\Http\Controllers\Petani\PengaturanController as PetaniPengaturanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================
// Landing Page
// ==========================
Route::get('/', function () {
    return view('landing.index');
})->name('landing');

Route::get('/setup-storage/{token}', function($token) {
    $validToken = env('STORAGE_LINK_TOKEN', '');
    if (empty($validToken) || $token !== $validToken) {
        abort(403);
    }
    
    // Remove old symlink
    if (is_link(public_path('storage'))) {
        unlink(public_path('storage'));
    }
    
    // Create new symlink
    $result = symlink(storage_path('app/public'), public_path('storage'));
    
    return $result ? 'Storage linked successfully!' : 'Failed to create symlink';
});



// ==========================
// Authentication Routes
// ==========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ==========================
// Midtrans Webhook
// PENTING: route ini HARUS di luar middleware auth
// Midtrans memanggil URL ini otomatis saat pembayaran berhasil
// ==========================
Route::post('/midtrans/webhook', [TransaksiController::class, 'webhook'])
    ->name('midtrans.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


// ==========================
// Dashboard Petani Routes
// ==========================
Route::middleware(['auth', 'role:petani'])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Sawah
        Route::get('/sawah', [SawahController::class, 'index'])->name('sawah');
        Route::post('/sawah', [SawahController::class, 'store'])->name('sawah.store');
        Route::put('/sawah/{id}', [SawahController::class, 'update'])->name('sawah.update');
        Route::delete('/sawah/{id}', [SawahController::class, 'destroy'])->name('sawah.destroy');

        // Prediksi
        Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi');
        Route::post('/prediksi', [PrediksiController::class, 'predict'])->name('prediksi.predict');

        // Perawatan
        Route::get('/perawatan', [PerawatanController::class, 'index'])->name('perawatan');
        Route::post('/perawatan', [PerawatanController::class, 'store'])->name('perawatan.store');
        Route::put('/perawatan/{id}', [PerawatanController::class, 'update'])->name('perawatan.update');
        Route::delete('/perawatan/{id}', [PerawatanController::class, 'destroy'])->name('perawatan.destroy');

        // Cuaca
        Route::get('/cuaca', [CuacaController::class, 'index'])->name('cuaca');
        Route::get('/cuaca/refresh', [CuacaController::class, 'refresh'])->name('cuaca.refresh');

        // Chatbot
        Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
        Route::post('/chatbot', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');

        // Riwayat
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
        Route::post('/riwayat', [RiwayatController::class, 'store'])->name('riwayat.store');
        Route::put('/riwayat/{id}', [RiwayatController::class, 'update'])->name('riwayat.update');
        Route::delete('/riwayat/{id}', [RiwayatController::class, 'destroy'])->name('riwayat.destroy');

        // Notifikasi
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');

        // Forum
        Route::get('/forum',             [ForumController::class, 'index'])->name('forum');
        Route::get('/forum/{id}',        [ForumController::class, 'show'])->name('forum.show');
        Route::post('/forum',            [ForumController::class, 'store'])->name('forum.store');
        Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');
        Route::post('/forum/{id}/like',  [ForumController::class, 'like'])->name('forum.like');

        // Transaksi Premium
        Route::get('/transaksi',                   [TransaksiController::class, 'index'])->name('transaksi');
        Route::post('/transaksi',                  [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/finish',            [TransaksiController::class, 'finish'])->name('transaksi.finish');
        Route::get('/transaksi/{id}',              [TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/transaksi/{id}/cek-status',   [TransaksiController::class, 'cekStatus'])->name('transaksi.cek');

        // Pengaturan Profil Petani
        Route::get('/pengaturan',          [PetaniPengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan',          [PetaniPengaturanController::class, 'update'])->name('pengaturan.update');
        Route::post('/pengaturan/foto',    [PetaniPengaturanController::class, 'updateFoto'])->name('pengaturan.foto');
        Route::put('/pengaturan/password', [PetaniPengaturanController::class, 'updatePassword'])->name('pengaturan.password');
    });


// ==========================
// Admin Routes
// ==========================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

        // Petani Management
        Route::get('/petani', [PetaniController::class, 'index'])->name('petani');
        Route::post('/petani', [PetaniController::class, 'store'])->name('petani.store');
        Route::put('/petani/{id}', [PetaniController::class, 'update'])->name('petani.update');
        Route::delete('/petani/{id}', [PetaniController::class, 'destroy'])->name('petani.destroy');
        Route::get('/petani/{id}', [PetaniController::class, 'show'])->name('petani.show');

        // Sawah Management
        Route::get('/sawah', [AdminSawahController::class, 'index'])->name('sawah');
        Route::get('/sawah/{id}', [AdminSawahController::class, 'show'])->name('sawah.show');
        Route::post('/sawah/{id}/verifikasi-lulus', [AdminSawahController::class, 'verifikasiLulus'])->name('sawah.verifikasi.lulus');
        Route::post('/sawah/{id}/verifikasi-tolak', [AdminSawahController::class, 'verifikasiTolak'])->name('sawah.verifikasi.tolak');
        Route::post('/sawah/{id}/verifikasi-reset', [AdminSawahController::class, 'verifikasiReset'])->name('sawah.verifikasi.reset');
        Route::post('/sawah/{id}/notifikasi', [AdminSawahController::class, 'kirimNotifikasi'])->name('sawah.notifikasi');
        Route::post('/sawah/broadcast', [AdminSawahController::class, 'broadcast'])->name('sawah.broadcast');
        Route::delete('/sawah/{id}', [AdminSawahController::class, 'destroy'])->name('sawah.destroy');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

        // Pengaturan Sistem + Profil Admin
        Route::get('/pengaturan',          [PengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan',          [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::put('/pengaturan/profil',   [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
        Route::post('/pengaturan/foto',    [PengaturanController::class, 'updateFoto'])->name('pengaturan.foto');
        Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');

        // Profil Admin
        Route::get('/profil',          [AdminProfilController::class, 'index'])->name('profil');
        Route::put('/profil',          [AdminProfilController::class, 'update'])->name('profil.update');
        Route::post('/profil/foto',    [AdminProfilController::class, 'updateFoto'])->name('profil.foto');
        Route::put('/profil/password', [AdminProfilController::class, 'updatePassword'])->name('profil.password');

        // Forum Management
        Route::get('/forum', [AdminForumController::class, 'index'])->name('forum');
        Route::get('/forum/{id}', [AdminForumController::class, 'show'])->name('forum.show');
        Route::post('/forum/{id}/pin', [AdminForumController::class, 'togglePin'])->name('forum.pin');
        Route::post('/forum/{id}/lock', [AdminForumController::class, 'toggleLock'])->name('forum.lock');
        Route::post('/forum/{id}/hot', [AdminForumController::class, 'toggleHot'])->name('forum.hot');
        Route::post('/forum/{id}/reply', [AdminForumController::class, 'reply'])->name('forum.reply');
        Route::delete('/forum/reply/{id}', [AdminForumController::class, 'destroyReply'])->name('forum.reply.destroy');
        Route::delete('/forum/{id}', [AdminForumController::class, 'destroy'])->name('forum.destroy');

        // Transaksi Management
        Route::get('/transaksi', [TransaksiAdminController::class, 'index'])->name('transaksi');
        Route::post('/transaksi/{id}/konfirmasi', [TransaksiAdminController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
        Route::post('/transaksi/{id}/tolak', [TransaksiAdminController::class, 'tolak'])->name('transaksi.tolak');
        Route::delete('/transaksi/{id}', [TransaksiAdminController::class, 'hapus'])->name('transaksi.hapus');
    });