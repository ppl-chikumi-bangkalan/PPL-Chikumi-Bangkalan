<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * Sambungan halaman kasir & owner ke backend (render halaman + ringkasan API).
 */
class DashboardWiringTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->buatDataChikumi();
        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.uji@gmail.com',
            'phone_number' => '081200000003',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id');
            $table->string('delivery_method');
            $table->text('shipping_address')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->string('status')->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_halaman_kasir_dan_owner_merender_data_backend(): void
    {
        $kasir = $this->buatKasir();

        $this->actingAs($kasir)->get('/kasir/dashboard')
            ->assertOk()
            ->assertSee('Chikumi Original Box');

        $this->actingAs($this->owner)->get('/owner/dashboard')
            ->assertOk()
            ->assertSee('Kondisi Stok');
    }

    public function test_ringkasan_owner_menghitung_omzet_pengeluaran_laba_dan_transaksi(): void
    {
        DB::table('pos_transactions')->insert([
            'transaction_number' => 'POS-TEST-1',
            'cashier_id' => $this->owner->id,
            'total_amount' => 100000,
            'payment_method' => 'cash',
            'amount_paid' => 100000,
            'change_amount' => 0,
            'status' => 'paid',
            'created_at' => now(),
        ]);
        DB::table('orders')->insert([
            'order_number' => 'ORD-TEST-1',
            'customer_id' => $this->owner->id,
            'delivery_method' => 'pickup',
            'subtotal' => 50000,
            'shipping_cost' => 0,
            'grand_total' => 50000,
            'status' => 'completed',
            'paid_at' => now(),
            'created_at' => now(),
        ]);
        DB::table('expenses')->insert([
            'category' => 'operational',
            'title' => 'Listrik',
            'amount' => 40000,
            'expense_date' => now()->toDateString(),
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->owner)->getJson('/owner/api/dashboard?periode=bulan');

        $response->assertOk();
        $this->assertEquals(150000, $response->json('data.omzet'));
        $this->assertEquals(40000, $response->json('data.pengeluaran'));
        $this->assertEquals(110000, $response->json('data.laba'));
        $this->assertEquals(2, $response->json('data.transaksi.total'));
        $this->assertCount(5, $response->json('data.grafik.label'));
    }
}
