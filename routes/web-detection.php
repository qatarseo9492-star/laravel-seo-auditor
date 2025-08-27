<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentDetectionController;

// UI pages
Route::get('/detection', function () {
    return view('detection.index');
})->name('detection.index');

Route::get('/detection/history', [ContentDetectionController::class, 'history'])->name('detection.history');
