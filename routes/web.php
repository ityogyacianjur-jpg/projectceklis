<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

    Route::get('/master-user', [UserController::class, 'index'])->name('users.index');
    Route::get('/api/users', [UserController::class, 'getData']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{id}', [UserController::class, 'update']);
    Route::delete('/api/users/{id}', [UserController::class, 'destroy']);
    });

    // Area Khusus Administrator
    Route::middleware('can:is-admin')->group(function () {
        // Rute untuk master user dan manajemen program ditaruh di sini
        // Rute Master User CRUD
    
});