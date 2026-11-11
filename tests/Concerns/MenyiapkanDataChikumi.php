<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema & data uji untuk testing PPL-20 (Harga Dinamis Sesuai Pilihan).
 *
 * Proyek memakai database/migrate.php (MySQL mentah), bukan migration Laravel,
 * sehingga tabel yang dibutuhkan tes dibuat ulang di SQLite in-memory
 * (lihat phpunit.xml) dengan kolom yang sama seperti migrate.php.
 */
trait MenyiapkanDataChikumi
{
    protected function buatSkemaChikumi(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone_number', 20)->nullable()->unique();
            $table->string('password');
            $table->string('role')->default('customer');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('type');
            $table->string('name', 100);
            $table->decimal('additional_price', 12, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->string('size_name');
            $table->decimal('base_price', 12, 2);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('product_size_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('cart_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id');
            $table->foreignId('variant_id');
        });
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_size_id');
            $table->foreignId('user_id')->nullable();
            $table->string('type');
            $table->integer('quantity');
            $table->string('source');
            $table->foreignId('reference_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('cashier_id');
            $table->string('customer_name')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method');
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_amount', 12, 2);
            $table->string('status')->default('paid');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_id');
            $table->foreignId('product_size_id');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 2);
        });
        Schema::create('pos_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_item_id');
            $table->foreignId('variant_id');
            $table->decimal('variant_price', 12, 2);
        });
    }

    /**
     * Data sama dengan database/seed.php, ditambah 1 seasoning berbayar,
     * 1 varian habis, dan 1 produk lain untuk uji kepemilikan.
     *
     * @return array<string, mixed>
     */
    protected function buatDataChikumi(): array
    {
        $kategori = Category::create(['name' => 'Chikumi Main Menu']);

        $produk = Product::create([
            'category_id' => $kategori->id,
            'name' => 'Chikumi Original Box',
            'description' => 'Ayam crispy lezat khas Chikumi',
            'image_url' => 'chikumi.jpg',
            'is_active' => true,
        ]);

        $regular = ProductSize::create(['product_id' => $produk->id, 'size_name' => 'Regular', 'base_price' => 15000, 'stock' => 20, 'min_stock' => 5]);
        $large = ProductSize::create(['product_id' => $produk->id, 'size_name' => 'Large', 'base_price' => 22000, 'stock' => 3, 'min_stock' => 5]);

        $varian = fn (string $type, string $name, int $harga, bool $tersedia = true) => Variant::create([
            'product_id' => $produk->id,
            'type' => $type,
            'name' => $name,
            'additional_price' => $harga,
            'is_available' => $tersedia,
        ]);

        $produkLain = Product::create(['category_id' => $kategori->id, 'name' => 'Chikumi Spicy Box', 'is_active' => true]);
        $ukuranProdukLain = ProductSize::create(['product_id' => $produkLain->id, 'size_name' => 'Regular', 'base_price' => 17000, 'stock' => 10]);
        $varianProdukLain = Variant::create(['product_id' => $produkLain->id, 'type' => 'filling', 'name' => 'Keju Lain', 'additional_price' => 4000]);

        return [
            'produk' => $produk,
            'regular' => $regular,
            'large' => $large,
            'mozzarella' => $varian('filling', 'Mozzarella Cheese', 3000),
            'cokelat' => $varian('filling', 'Cokelat Lumer', 3500),
            'bbq' => $varian('dipping_sauce', 'Barbeque Sauce', 2000),
            'cheeseSauce' => $varian('dipping_sauce', 'Cheese Sauce', 2500),
            'balado' => $varian('seasoning', 'Balado Extra Pedas', 0),
            'kejuBubuk' => $varian('seasoning', 'Keju Bubuk', 1000),
            'sambalHabis' => $varian('dipping_sauce', 'Sambal Matah', 1500, false),
            'produkLain' => $produkLain,
            'ukuranProdukLain' => $ukuranProdukLain,
            'varianProdukLain' => $varianProdukLain,
        ];
    }

    protected function buatPelanggan(): User
    {
        return User::create([
            'name' => 'Pelanggan Uji',
            'email' => 'pelanggan.uji@gmail.com',
            'phone_number' => '081200000001',
            'password' => 'rahasia123',
            'role' => 'customer',
        ]);
    }

    protected function buatKasir(): User
    {
        return User::create([
            'name' => 'Kasir Uji',
            'email' => 'kasir.uji@gmail.com',
            'phone_number' => '081200000002',
            'password' => 'rahasia123',
            'role' => 'cashier',
        ]);
    }
}
