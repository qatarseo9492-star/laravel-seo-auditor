<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoAuditController;

Route::get('/', function () {
    return redirect()->route('seo.audit');
});

Route::get('/seo-audit', [SeoAuditController::class, 'index'])->name('seo.audit');
