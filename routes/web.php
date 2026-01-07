<?php

use App\Http\Controllers\TrendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TrendController::class, 'index'])->name('trends.index');
Route::get('/trending', [TrendController::class, 'trending'])->name('trends.trending');
Route::get('/about', [TrendController::class, 'about'])->name('trends.about');
Route::get('/category/{slug}', [TrendController::class, 'category'])->name('trends.category');
