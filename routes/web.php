<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\TugasController;
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\ClassIndex as GuruClassIndex;
use App\Livewire\Guru\ClassDetail as GuruClassDetail;

use App\Livewire\Siswa\KelasSaya;
use App\Livewire\Siswa\JelajahiKelas;
use App\Livewire\Siswa\KumpulkanTugas;

// Halaman Login
Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

// Dashboard berdasarkan Role
Route::get('/dashboard', function () {
    return match (request()->user()->role) {
        'super_admin' => view('SuperAdmin.dashboard'),
        'student' => redirect()->route('siswa.dashboard'),
        'school_admin' => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('guru.dashboard'),
        default => abort(403, 'Role tidak dikenali.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Rute Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Rute Super Admin
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::resource('tenants', TenantController::class);
        Route::patch('tenants/{tenant}/status', [TenantController::class, 'toggleStatus'])
            ->name('tenants.status');
        Route::view('/dashboard', 'SuperAdmin.dashboard')
            ->name('dashboard');
    });

// Rute Authentication
require __DIR__ . '/auth.php';

// Rute khusus Siswa
Route::middleware(['auth', 'role:student'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        // Dashboard Siswa (data dari database)
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])
            ->name('dashboard');

        // Kelas
        Route::get('/kelas-saya', KelasSaya::class)
            ->name('kelas-saya');

        Route::get('/jelajahi', JelajahiKelas::class)
            ->name('jelajahi');

        // Daftar Tugas
        Route::get('/tugas', [TugasController::class, 'index'])
            ->name('tugas');

        // Detail dan Pengumpulan Tugas
        Route::get('/tugas/{assignment}', KumpulkanTugas::class)
            ->name('tugas.kumpul');
    });

// Rute khusus Guru
Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', GuruDashboard::class)
            ->name('dashboard');

        // Kelas milik guru
        Route::get('/kelas', GuruClassIndex::class)
            ->name('classes.index');

        Route::get('/kelas/{kelas}', GuruClassDetail::class)
            ->whereNumber('kelas')
            ->name('classes.show');
    });

// Rute Modular Admin Sekolah
// Dashboard admin sekolah (Livewire) didefinisikan di file modul ini.
require base_path('app/Modules/AdminSekolah/routes/web.php');