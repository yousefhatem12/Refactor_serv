<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ar', function () {
    return view('welcome');
});

Route::get('/en', function () {
    return view('welcome');
});
