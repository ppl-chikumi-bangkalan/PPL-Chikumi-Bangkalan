<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ProductSizeController;
use App\Http\Controllers\Owner\VariantController;
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
        return view('owner.dashboard');
    })->middleware('role:owner')->name('owner.dashboard');

    Route::get('/kasir/dashboard', function () {
        return view('dashboard');
    })->middleware('role:owner,cashier')->name('kasir.dashboard');

    Route::get('/customer/dashboard', function () {
        return view('dashboard');
    })->middleware('role:customer')->name('customer.dashboard');

    Route::get('/owner/kelola-produk', function () {
        return view('owner.produk');
    })->middleware('role:owner')->name('owner.kelola-produk');

    Route::prefix('owner/api')->middleware('role:owner')->group(function () {
        Route::get('produk', [ProductController::class, 'index'])->name('owner.produk.index');
        Route::post('produk', [ProductController::class, 'store'])->name('owner.produk.store');
        Route::get('produk/{produk}', [ProductController::class, 'show'])->whereNumber('produk')->name('owner.produk.show');
        Route::put('produk/{produk}', [ProductController::class, 'update'])->whereNumber('produk')->name('owner.produk.update');
        Route::delete('produk/{produk}', [ProductController::class, 'destroy'])->whereNumber('produk')->name('owner.produk.destroy');

        Route::post('produk/{produk}/ukuran', [ProductSizeController::class, 'store'])->whereNumber('produk')->name('owner.ukuran.store');
        Route::put('ukuran/{ukuran}', [ProductSizeController::class, 'update'])->whereNumber('ukuran')->name('owner.ukuran.update');
        Route::patch('ukuran/{ukuran}/stok', [ProductSizeController::class, 'updateStok'])->whereNumber('ukuran')->name('owner.ukuran.stok');
        Route::delete('ukuran/{ukuran}', [ProductSizeController::class, 'destroy'])->whereNumber('ukuran')->name('owner.ukuran.destroy');

        Route::post('produk/{produk}/varian', [VariantController::class, 'store'])->whereNumber('produk')->name('owner.varian.store');
        Route::put('varian/{varian}', [VariantController::class, 'update'])->whereNumber('varian')->name('owner.varian.update');
        Route::patch('varian/{varian}/status', [VariantController::class, 'updateStatus'])->whereNumber('varian')->name('owner.varian.status');
        Route::delete('varian/{varian}', [VariantController::class, 'destroy'])->whereNumber('varian')->name('owner.varian.destroy');
    });
});

// FRONTEND DEVELOPMENT (sementara, tanpa login)
Route::get('/frontend/owner/dashboard', function () {
    return view('owner.dashboard');
})->name('frontend.owner');

Route::get('/frontend/owner/produk', function () {
    return redirect()->route('owner.kelola-produk');
})->name('frontend.owner');