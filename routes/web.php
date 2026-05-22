<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChecklistController;

Route::get('/', [ChecklistController::class, 'index']);
Route::get('/api/checklists', [ChecklistController::class, 'getData']);
Route::post('/api/checklists', [ChecklistController::class, 'saveData']);
Route::post('/api/checklists/reset', [ChecklistController::class, 'resetData']);

// Route baru untuk tambah & hapus poin
Route::post('/api/checklists/add', [ChecklistController::class, 'addItem']);
Route::delete('/api/checklists/{id}', [ChecklistController::class, 'deleteItem']);