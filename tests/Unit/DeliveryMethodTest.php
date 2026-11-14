<?php

namespace Tests\Unit;

use App\Support\DeliveryMethod;
use Tests\TestCase;

/**
 * [CHIK-103] Testing metode penerimaan — PPL-24 (backend CHIK-101).
 *
 * Unit test App\Support\DeliveryMethod (tarif & aturan diantar / ambil di tempat).
 */
class DeliveryMethodTest extends TestCase
{
    public function test_kode_metode_hanya_pickup_dan_courier(): void
    {
        $this->assertSame(['pickup', 'courier'], DeliveryMethod::codes());
    }

    public function test_ongkir_courier_dan_pickup(): void
    {
        $this->assertSame(5000.0, DeliveryMethod::shippingCost('courier'));
        $this->assertSame(0.0, DeliveryMethod::shippingCost('pickup'));
    }

    public function test_ongkir_kode_tidak_dikenal_menjadi_nol(): void
    {
        $this->assertSame(0.0, DeliveryMethod::shippingCost('drone'));
    }

    public function test_hanya_courier_yang_butuh_alamat(): void
    {
        $this->assertTrue(DeliveryMethod::requiresAddress('courier'));
        $this->assertFalse(DeliveryMethod::requiresAddress('pickup'));
    }

    public function test_opsi_metode_menyediakan_label_dan_flag_alamat(): void
    {
        $options = DeliveryMethod::options();

        $this->assertCount(2, $options);

        $courier = $options[0];
        $this->assertSame('courier', $courier['kode']);
        $this->assertSame('Diantar', $courier['label']);
        $this->assertSame(5000.0, $courier['ongkir']);
        $this->assertTrue($courier['butuh_alamat']);

        $pickup = $options[1];
        $this->assertSame('pickup', $pickup['kode']);
        $this->assertSame('Ambil di tempat', $pickup['label']);
        $this->assertSame(0.0, $pickup['ongkir']);
        $this->assertFalse($pickup['butuh_alamat']);
    }
}