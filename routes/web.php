<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AuthController;

// Rute Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute yang membutuhkan Login
Route::middleware('auth')->group(function () {
    
    // Halaman Checklist (Bisa diakses Admin & Supervisor)
    Route::get('/', [ChecklistController::class, 'index']);
    Route::get('/api/checklists', [ChecklistController::class, 'getData']);
    Route::post('/api/checklists', [ChecklistController::class, 'saveData']);
    Route::post('/api/checklists/reset', [ChecklistController::class, 'resetData']);
    Route::post('/api/checklists/add', [ChecklistController::class, 'addItem']);
    Route::delete('/api/checklists/{id}', [ChecklistController::class, 'deleteItem']);

    // Area Khusus Administrator
    Route::middleware('can:is-admin')->group(function () {
        // Rute untuk master user dan manajemen program ditaruh di sini
        // Contoh:
        // Route::get('/master-user', [UserController::class, 'index']);
        // Route::post('/master-user', [UserController::class, 'store']);
    });
});