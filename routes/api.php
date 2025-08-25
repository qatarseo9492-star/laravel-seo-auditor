<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectController;

Route::post('/detect', DetectController::class)->name('detect');
