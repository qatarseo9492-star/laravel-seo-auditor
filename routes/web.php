<?php

use Illuminate\Support\Facades\Route;

// Home page -> resources/views/home.blade.php
Route::view('/', 'home')->name('home');
