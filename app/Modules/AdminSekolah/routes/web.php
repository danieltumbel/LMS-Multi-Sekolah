<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AdminSekolah\Http\Livewire\Dashboard;
use App\Modules\AdminSekolah\Http\Livewire\ManageUsers;

Route::middleware(['auth', 'verified', 'role:school_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // Manajemen pengguna: tab Guru dan Siswa memakai komponen yang sama
        Route::get('/guru', ManageUsers::class)->defaults('role', 'teacher')->name('teachers.index');
        Route::get('/siswa', ManageUsers::class)->defaults('role', 'student')->name('students.index');
    });

// Alamat lama diarahkan ke dashboard baru
Route::redirect('/admin-sekolah/dashboard', '/admin/dashboard')
    ->name('admin-sekolah.dashboard');