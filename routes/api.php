<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectController;
use App\Http\Controllers\PsiController;

// Existing: AI/Human detector
Route::post('/detect', [DetectController::class, 'detect'])->name('detect');

// New: PageSpeed Insights proxy (served at /api/psi)
// GET /api/psi?url=https://example.com&strategy=mobile&category[]=performance&category[]=accessibility&category[]=seo&category[]=best-practices
// The controller reads PAGESPEED_API_KEY from .env via config('services.pagespeed.key')
Route::get('/psi', [PsiController::class, 'run'])
    ->name('api.psi')
    ->middleware('throttle:30,1'); // protect quota a bit
