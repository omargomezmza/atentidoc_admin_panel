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

    Route::get('/user-list/doctors', [\App\Http\Controllers\UserController::class, 'doctor_index'])->name('admin.list.doctor');
    Route::get('/user-list/patients', [\App\Http\Controllers\UserController::class, 'patient_index'])->name('admin.list.patient');
    Route::get('/user-list/admins', [\App\Http\Controllers\UserController::class, 'admin_index'])->name('admin.list.admin');

    Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create_user'])->name('admin.create.user');
    Route::post('/users/store', [\App\Http\Controllers\UserController::class, 'store_user'])->name('admin.store.user');


    Route::get('/users/show/{user_id?}', [\App\Http\Controllers\UserController::class, 'show_user'])->name('admin.show.user');
    
    Route::get('/users/edit/{user_id?}', [\App\Http\Controllers\UserController::class, 'edit_user'])->name('admin.edit.user');
    Route::patch('/users/update/{user_id?}', [\App\Http\Controllers\UserController::class, 'update_user'])->name('admin.update.user');
    
    Route::delete('/users/delete/{user_id?}', [\App\Http\Controllers\UserController::class, 'delete_user'])->name('admin.delete.user');

    
});