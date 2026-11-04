<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AdminSekolah\Http\Livewire\ClassForm;
use App\Modules\AdminSekolah\Http\Livewire\Dashboard;
use App\Modules\AdminSekolah\Http\Livewire\ManageClasses;
use App\Modules\AdminSekolah\Http\Livewire\ManageUsers;
use App\Modules\AdminSekolah\Http\Livewire\TeacherForm;

Route::middleware(['auth', 'verified', 'role:school_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // Manajemen pengguna: tab Guru dan Siswa memakai komponen yang sama
        Route::get('/guru', ManageUsers::class)->defaults('role', 'teacher')->name('teachers.index');
        Route::get('/guru/tambah', TeacherForm::class)->name('teachers.create');
        Route::get('/siswa', ManageUsers::class)->defaults('role', 'student')->name('students.index');

        // Kelas / Rombel
        Route::get('/kelas', ManageClasses::class)->name('classes.index');
        Route::get('/kelas/tambah', ClassForm::class)->name('classes.create');
    });

// Alamat lama diarahkan ke dashboard baru
Route::redirect('/admin-sekolah/dashboard', '/admin/dashboard')
    ->name('admin-sekolah.dashboard');