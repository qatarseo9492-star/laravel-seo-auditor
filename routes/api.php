<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectController;

Route::post('/detect', [DetectController::class, 'detect'])->name('detect');
// If you prefer the api-style name/path instead, use one of these (and update the Blade to match):
// Route::post('/api/detect', [DetectController::class, 'detect'])->name('api.detect');
