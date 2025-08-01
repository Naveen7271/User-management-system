<?php

use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Frontend Routes
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/login', [FrontendController::class, 'login'])->name('login');
Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');
Route::get('/users', [FrontendController::class, 'users'])->name('users');
Route::get('/create-user', [FrontendController::class, 'createUser'])->name('create-user');
Route::get('/bulk-create', [FrontendController::class, 'bulkCreate'])->name('bulk-create');

// Catch-all route for SPA (Single Page Application)
Route::get('/{any}', [FrontendController::class, 'index'])->where('any', '.*');
