<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return 'Halo, ini halaman utama Laravel!';
});

Route::get('/login', function () {
    return view('auth.login');
});