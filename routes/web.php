<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// LANDING SETELAH LOGIN / REGISTER (dipisah per hak akses role)
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'owner' => redirect()->route('owner.dashboard'),
        'cashier' => redirect()->route('kasir.dashboard'),
        default => redirect()->route('customer.dashboard'),
    };
})->name('home');

// HALAMAN & PROSES AUTH (cuma bisa diakses tamu)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', LoginController::class)->name('login.store');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', RegisterController::class)->name('register.store');
});

// KELUAR
Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// AREA DENGAN HAK AKSES (placeholder berisi tombol logout sementara)
Route::middleware('auth')->group(function () {
    Route::get('/owner/dashboard', function () {
        return view('dashboard');
    })->middleware('role:owner')->name('owner.dashboard');

    Route::get('/kasir/dashboard', function () {
        return view('dashboard');
    })->middleware('role:owner,cashier')->name('kasir.dashboard');

    Route::get('/customer/dashboard', function () {
        return view('dashboard');
    })->middleware('role:customer')->name('customer.dashboard');
});

// FRONTEND DEVELOPMENT (sementara, tanpa login)
Route::get('/frontend/owner/dashboard', function () {
    return view('owner.dashboard');
})->name('frontend.owner');

Route::get('/frontend/owner/produk', function () {
    return view('owner.produk');
})->name('frontend.owner');