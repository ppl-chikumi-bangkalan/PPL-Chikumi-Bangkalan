<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CatalogController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\TransactionController as KasirTransactionController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ProductSizeController;
use App\Http\Controllers\Owner\VariantController;
use App\Models\Product;
use Illuminate\Http\Request;
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
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'page'])
        ->middleware('role:owner')->name('owner.dashboard');

    Route::get('/kasir/dashboard', [KasirDashboardController::class, 'page'])
        ->middleware('role:owner,cashier')->name('kasir.dashboard');
    Route::post('/kasir/api/transaksi', [KasirTransactionController::class, 'store'])
        ->middleware('role:owner,cashier')->name('kasir.transaksi.store');

    Route::get('/customer/dashboard', [DashboardController::class, 'page'])
        ->middleware('role:customer')->name('customer.dashboard');
    Route::get('/customer/api/katalog', [CatalogController::class, 'index'])->name('customer.katalog');

    Route::get('/customer/produk', function (Request $request) {
        $produk = Product::with(['sizes', 'variants'])
            ->where('is_active', true)
            ->when($request->integer('produk'), fn ($query, $id) => $query->whereKey($id))
            ->orderBy('id')
            ->first();

        return view('customer.produk', ['produk' => $produk]);
    })->middleware('role:customer')->name('customer.produk');

    // Tanpa nama route: view lama memanggil route('customer.produk', ['ukuran'=>...])
    // yang tidak cocok parameternya -> UrlGenerationException loop. Saat wiring API,
    // pakai /customer/api/produk/{id_produk} langsung (lihat docs/API-KATALOG.md).
    Route::get('/customer/api/produk/{produk}', [CustomerProductController::class, 'show'])->whereNumber('produk');
    Route::get('/customer/api/produk/{produk}/harga', [CustomerProductController::class, 'price'])->whereNumber('produk');
    Route::prefix('customer/api')->middleware('role:customer')->group(function () {
        Route::get('keranjang', [CartController::class, 'index'])->name('customer.keranjang.index');
        Route::post('keranjang', [CartController::class, 'store'])->name('customer.keranjang.store');
        Route::put('keranjang/{keranjang}', [CartController::class, 'update'])->whereNumber('keranjang')->name('customer.keranjang.update');
        Route::delete('keranjang/{keranjang}', [CartController::class, 'destroy'])->whereNumber('keranjang')->name('customer.keranjang.destroy');
        Route::get('pesanan', [OrderController::class, 'index'])->name('customer.pesanan.index');
        Route::post('pesanan/checkout', [OrderController::class, 'checkout'])->name('customer.pesanan.checkout');
        Route::patch('pesanan/{pesanan}/batal', [OrderController::class, 'cancel'])->whereNumber('pesanan')->name('customer.pesanan.batal');
        Route::get('alamat', [AddressController::class, 'index'])->name('customer.alamat.index');
        Route::post('alamat', [AddressController::class, 'store'])->name('customer.alamat.store');
        Route::put('alamat/{alamat}', [AddressController::class, 'update'])->whereNumber('alamat')->name('customer.alamat.update');
        Route::delete('alamat/{alamat}', [AddressController::class, 'destroy'])->whereNumber('alamat')->name('customer.alamat.destroy');
    });

    Route::get('/owner/produk/{produk}/edit/{ukuran?}', [ProductController::class, 'pageEdit'])
        ->whereNumber('produk')->whereNumber('ukuran')->middleware('role:owner')->name('owner.produk.edit');

    Route::get('/owner/produk', [ProductController::class, 'pageIndex'])
        ->middleware('role:owner')->name('owner.produk');

    Route::get('/owner/stok', [ProductController::class, 'pageStok'])
        ->middleware('role:owner')->name('owner.stok');

    Route::prefix('owner/api')->middleware('role:owner')->group(function () {
        Route::get('dashboard', [OwnerDashboardController::class, 'summary'])->name('owner.dashboard.api');
        Route::get('produk', [ProductController::class, 'index'])->name('owner.produk.index');
        Route::post('produk', [ProductController::class, 'store'])->name('owner.produk.store');
        Route::get('produk/ketersediaan', [ProductController::class, 'availability'])->name('owner.produk.ketersediaan');

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
Route::get('/frontend/owner/dashboard', [OwnerDashboardController::class, 'page'])
    ->name('frontend.owner');
Route::get('/frontend/owner/api/dashboard', [OwnerDashboardController::class, 'summary']);
Route::get('/frontend/owner/api/ketersediaan', [ProductController::class, 'availability']);
Route::get('/frontend/customer/dashboard', [DashboardController::class, 'page'])
    ->name('frontend.customer.dashboard');
Route::get('/frontend/customer/api/katalog', [CatalogController::class, 'index'])
    ->name('frontend.customer.katalog');
Route::get('/frontend/customer/produk', function (Request $request) {
    $produk = Product::with(['sizes', 'variants'])
        ->where('is_active', true)
        ->when($request->integer('produk'), fn ($query, $id) => $query->whereKey($id))
        ->orderBy('id')
        ->first();

    return view('customer.produk', ['produk' => $produk]);
})->name('frontend.customer.produk');
Route::get('/frontend/kasir/dashboard', [KasirDashboardController::class, 'page'])
    ->name('frontend.kasir.dashboard');
// API dev kasir (tanpa login; kirim header `X-Dev-Kasir: <id kasir/owner>`)
Route::post('/frontend/kasir/api/transaksi', [KasirTransactionController::class, 'store']);

// API dev pelanggan (tanpa login; kirim header `X-Dev-Customer: <id pelanggan>` untuk data milik pelanggan tsb)
Route::prefix('frontend/customer/api')->group(function () {
    Route::get('produk/{produk}', [CustomerProductController::class, 'show'])->whereNumber('produk');
    Route::get('produk/{produk}/harga', [CustomerProductController::class, 'price'])->whereNumber('produk');
    Route::get('keranjang', [CartController::class, 'index']);
    Route::post('keranjang', [CartController::class, 'store']);
    Route::put('keranjang/{keranjang}', [CartController::class, 'update'])->whereNumber('keranjang');
    Route::delete('keranjang/{keranjang}', [CartController::class, 'destroy'])->whereNumber('keranjang');
    Route::get('pesanan', [OrderController::class, 'index']);
    Route::post('pesanan/checkout', [OrderController::class, 'checkout']);
    Route::patch('pesanan/{pesanan}/batal', [OrderController::class, 'cancel'])->whereNumber('pesanan');
    Route::get('alamat', [AddressController::class, 'index']);
    Route::post('alamat', [AddressController::class, 'store']);
    Route::put('alamat/{alamat}', [AddressController::class, 'update'])->whereNumber('alamat');
    Route::delete('alamat/{alamat}', [AddressController::class, 'destroy'])->whereNumber('alamat');
});
