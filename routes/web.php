<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Superadmin;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:30,1')->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:30,1')->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [User\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/riwayat', [User\DashboardController::class, 'riwayat'])->name('riwayat');
    Route::get('/faq', [User\FaqController::class, 'index'])->name('faq');
    Route::post('/complains', [User\ComplainController::class, 'store'])->name('complains.store');
    Route::get('/complains/{complain}', [User\ComplainController::class, 'show'])->name('complains.show');
    Route::delete('/complains/{complain}', [User\ComplainController::class, 'destroy'])->name('complains.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/complains', [Admin\ComplainController::class, 'index'])->name('complains');
    Route::post('/complains/{complain}/respond', [Admin\ComplainController::class, 'respond'])->name('complains.respond');
    Route::delete('/complains/{complain}', [Admin\ComplainController::class, 'destroy'])->name('complains.destroy');
    Route::get('/responses', [Admin\ResponseController::class, 'index'])->name('responses');
    Route::delete('/responses/{complain}', [Admin\ResponseController::class, 'destroy'])->name('responses.destroy');
});

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [Superadmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/complains', [Superadmin\ComplainController::class, 'index'])->name('complains');
    Route::post('/complains/{complain}/respond', [Superadmin\ComplainController::class, 'respond'])->name('complains.respond');
    Route::delete('/complains/{complain}', [Superadmin\ComplainController::class, 'destroy'])->name('complains.destroy');
    Route::get('/responses', [Superadmin\ResponseController::class, 'index'])->name('responses');
    Route::delete('/responses/{complain}', [Superadmin\ResponseController::class, 'destroy'])->name('responses.destroy');

    Route::get('/users', [Superadmin\UserController::class, 'index'])->name('users');
    Route::post('/users', [Superadmin\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [Superadmin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [Superadmin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/admins', [Superadmin\AdminUserController::class, 'index'])->name('admins');
    Route::post('/admins', [Superadmin\AdminUserController::class, 'store'])->name('admins.store');
    Route::put('/admins/{admin}', [Superadmin\AdminUserController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{admin}', [Superadmin\AdminUserController::class, 'destroy'])->name('admins.destroy');

    Route::get('/tujuans', [Superadmin\TujuanController::class, 'index'])->name('tujuans');
    Route::post('/tujuans', [Superadmin\TujuanController::class, 'store'])->name('tujuans.store');
    Route::put('/tujuans/{tujuan}', [Superadmin\TujuanController::class, 'update'])->name('tujuans.update');
    Route::delete('/tujuans/{tujuan}', [Superadmin\TujuanController::class, 'destroy'])->name('tujuans.destroy');

    Route::get('/jenis', [Superadmin\JenisController::class, 'index'])->name('jenis');
    Route::post('/jenis', [Superadmin\JenisController::class, 'store'])->name('jenis.store');
    Route::put('/jenis/{jeni}', [Superadmin\JenisController::class, 'update'])->name('jenis.update');
    Route::delete('/jenis/{jeni}', [Superadmin\JenisController::class, 'destroy'])->name('jenis.destroy');

    Route::get('/faqs', [Superadmin\FaqController::class, 'index'])->name('faqs');
    Route::post('/faqs', [Superadmin\FaqController::class, 'store'])->name('faqs.store');
    Route::put('/faqs/{faq}', [Superadmin\FaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [Superadmin\FaqController::class, 'destroy'])->name('faqs.destroy');

    Route::get('/laporan', [Superadmin\LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/cetak', [Superadmin\LaporanController::class, 'cetak'])->name('laporan.cetak');
});
