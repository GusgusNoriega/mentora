<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Zona protegida (ejemplo)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::view('/rbac', 'rbac.index')->name('rbac.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// (opcional) home redirige segun sesión
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});