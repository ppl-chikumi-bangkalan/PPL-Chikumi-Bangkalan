<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\MenyiapkanDataChikumi;
use Tests\TestCase;

/**
 * [CHIK-94] Uji penerimaan penambahan stok (PPL-09).
 *
 * Endpoint: PATCH /owner/api/ukuran/{ukuran}/stok (ProductSizeController::updateStok)
 * Halaman : GET /owner/stok (ProductController::pageStok)
 */
class PenambahanStokTest extends TestCase
{
    use MenyiapkanDataChikumi;

    private array $d;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buatSkemaChikumi();
        $this->d = $this->buatDataChikumi();
        $this->owner = User::create([
            'name' => 'Owner Uji',
            'email' => 'owner.uji@gmail.com',
            'phone_number' => '081200000003',
            'password' => 'rahasia123',
            'role' => 'owner',
        ]);
    }

    private function ubahStok(int $sizeId, array $body)
    {
        return $this->actingAs($this->owner)->patchJson("/owner/api/ukuran/{$sizeId}/stok", $body);
    }

    public function test_owner_menambah_stok_bertambah_dan_tercatat_di_riwayat(): void
    {
        $this->ubahStok($this->d['regular']->id, ['type' => 'in', 'quantity' => 5])
            ->assertOk()
            ->assertJsonPath('message', 'Stok ditambah, sekarang 25.')
            ->assertJsonPath('data.stock', 25);

        $this->assertSame(25, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'user_id' => $this->owner->id,
            'type' => 'in',
            'quantity' => 5,
            'source' => 'manual_restock',
        ]);
    }

    public function test_owner_menambah_stok_dengan_catatan_tersimpan(): void
    {
        $this->ubahStok($this->d['large']->id, [
            'type' => 'in',
            'quantity' => 10,
            'notes' => 'Persiapan buka 15.00',
        ])->assertOk();

        $this->assertSame(13, $this->d['large']->fresh()->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['large']->id,
            'type' => 'in',
            'quantity' => 10,
            'notes' => 'Persiapan buka 15.00',
        ]);
    }

    public function test_owner_mengurangi_stok(): void
    {
        $this->ubahStok($this->d['regular']->id, ['type' => 'out', 'quantity' => 3])
            ->assertOk()
            ->assertJsonPath('message', 'Stok dikurangi, sekarang 17.')
            ->assertJsonPath('data.stock', 17);

        $this->assertSame(17, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseHas('stock_histories', [
            'product_size_id' => $this->d['regular']->id,
            'type' => 'out',
            'quantity' => 3,
        ]);
    }

    public function test_kurangi_stok_melebihi_sisa_ditolak(): void
    {
        $this->ubahStok($this->d['regular']->id, ['type' => 'out', 'quantity' => 21])
            ->assertStatus(422);

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
        $this->assertDatabaseMissing('stock_histories', ['product_size_id' => $this->d['regular']->id]);
    }

    public function test_tipe_tidak_valid_ditolak(): void
    {
        $this->ubahStok($this->d['regular']->id, ['type' => 'transfer', 'quantity' => 5])
            ->assertStatus(422)
            ->assertJsonValidationErrors('type');

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
    }

    public function test_quantity_nol_ditolak(): void
    {
        $this->ubahStok($this->d['regular']->id, ['type' => 'in', 'quantity' => 0])
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
    }

    public function test_pelanggan_tidak_boleh_mengubah_stok(): void
    {
        $this->actingAs($this->buatPelanggan())
            ->patchJson("/owner/api/ukuran/{$this->d['regular']->id}/stok", [
                'type' => 'in',
                'quantity' => 5,
            ])->assertForbidden();

        $this->assertSame(20, $this->d['regular']->fresh()->stock);
    }

    public function test_halaman_stok_merender_form_dan_riwayat(): void
    {
        $this->actingAs($this->owner)->get('/owner/stok')
            ->assertOk()
            ->assertSee('Stok & Saran', false)
            ->assertSee('Riwayat Stok')
            ->assertSee('Simpan Stok')
            ->assertSee('Chikumi Original Box Regular');
    }
}