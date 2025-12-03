<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', fn () => view('admin.dashboard'));
//Route::get('/login', fn () => view('auth.login'));
//Route::post('/login', fn () => view('auth.login'))->name('login');

Route::get("/hola", fn () => "Hola");

// Rutas públicas
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    
    Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');
});