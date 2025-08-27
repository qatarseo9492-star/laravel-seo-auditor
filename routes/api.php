<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentDetectionController;

Route::middleware(['throttle:content-detection'])->group(function () {
    Route::post('/detect', [ContentDetectionController::class, 'detect']);
    Route::post('/detect/batch', [ContentDetectionController::class, 'detectBatch']);
    Route::get('/detections/{id}', [ContentDetectionController::class, 'show']);
});
