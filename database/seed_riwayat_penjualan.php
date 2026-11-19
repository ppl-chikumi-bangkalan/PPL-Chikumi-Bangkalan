<?php

require_once __DIR__.'/db.php';

function riwayat_pilih_bobot(array $bobot)
{
    $sisa = random_int(1, array_sum($bobot));
    foreach ($bobot as $nilai => $bobotnya) {
        $sisa -= $bobotnya;
        if ($sisa <= 0) {
            return $nilai;
        }
    }

    return array_key_first($bobot);
}

function riwayat_waktu(DateTimeImmutable $tanggal): ?DateTimeImmutable
{
    $mulai = $tanggal->setTime(9, 30);
    $selesai = $tanggal->setTime(21, 0);
    $sekarang = new DateTimeImmutable();

    if ($tanggal->format('Y-m-d') === $sekarang->format('Y-m-d')) {
        $selesai = $selesai < $sekarang ? $selesai : $sekarang->modify('-2 minutes');
    }

    if ($selesai <= $mulai) {
        return null;
    }

    return (new DateTimeImmutable())->setTimestamp(
        random_int($mulai->getTimestamp(), $selesai->getTimestamp())
    );
}

function riwayat_item(array $ukuran, array $varianPerProduk): array
{
    $opsi = $varianPerProduk[$ukuran['product_id']] ?? [];
    $dipilih = [];

    foreach (['filling' => 70, 'dipping_sauce' => 60] as $tipe => $persen) {
        if (! empty($opsi[$tipe]) && random_int(1, 100) <= $persen) {
            $dipilih[] = $opsi[$tipe][array_rand($opsi[$tipe])];
        }
    }

    $jumlahSeasoning = riwayat_pilih_bobot([0 => 50, 1 => 35, 2 => 15]);
    if (! empty($opsi['seasoning']) && $jumlahSeasoning > 0) {
        $seasoning = $opsi['seasoning'];
        shuffle($seasoning);
        foreach (array_slice($seasoning, 0, $jumlahSeasoning) as $varian) {
            $dipilih[] = $varian;
        }
    }

    $quantity = riwayat_pilih_bobot([1 => 50, 2 => 30, 3 => 15, 4 => 5]);
    $tambahan = array_sum(array_map(fn ($varian) => (float) $varian['additional_price'], $dipilih));
    $hargaSatuan = round((float) $ukuran['base_price'] + $tambahan, 2);

    return [
        'ukuran' => $ukuran,
        'varian' => $dipilih,
        'quantity' => $quantity,
        'unit_price' => $hargaSatuan,
        'subtotal' => round($hargaSatuan * $quantity, 2),
    ];
}

function riwayat_status_pesanan(int $umur): string
{
    $bobot = match (true) {
        $umur === 0 => ['unpaid' => 20, 'processing' => 25, 'ready' => 20, 'completed' => 30, 'cancelled' => 5],
        $umur === 1 => ['unpaid' => 5, 'processing' => 10, 'ready' => 10, 'completed' => 65, 'cancelled' => 10],
        default => ['completed' => 88, 'cancelled' => 7, 'unpaid' => 3, 'processing' => 2],
    };

    return riwayat_pilih_bobot($bobot);
}

$minggu = max(4, min(8, (int) ($argv[1] ?? 8)));

try {
    $ukuranList = $pdo->query("
        SELECT ps.id, ps.product_id, ps.size_name, ps.base_price
        FROM product_sizes ps
        JOIN products p ON p.id = ps.product_id
        WHERE p.is_active = 1
        ORDER BY ps.id
    ")->fetchAll();

    if (! $ukuranList) {
        throw new RuntimeException('Belum ada produk/ukuran aktif. Jalankan php database/migrate.php dan php database/seed.php dulu.');
    }

    $variants = $pdo->query("
        SELECT id, product_id, type, name, additional_price
        FROM variants
        WHERE is_available = 1
        ORDER BY id
    ")->fetchAll();

    $varianPerProduk = [];
    foreach ($variants as $varian) {
        $varianPerProduk[$varian['product_id']][$varian['type']][] = $varian;
    }

    $kasirIds = $pdo->query("SELECT id FROM users WHERE role IN ('cashier', 'owner') ORDER BY role = 'cashier' DESC, id")->fetchAll(PDO::FETCH_COLUMN);
    if (! $kasirIds) {
        throw new RuntimeException('Belum ada akun kasir/owner. Jalankan php database/seed.php dulu.');
    }

    $pelangganIds = $pdo->query("SELECT id FROM users WHERE role = 'customer' ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
    if (! $pelangganIds) {
        throw new RuntimeException('Belum ada akun pelanggan. Jalankan php database/seed.php dulu.');
    }

    $alamatPerPelanggan = [];
    $alamatRows = $pdo->query("
        SELECT customer_id, recipient_name, phone, detail, city, postal_code
        FROM delivery_addresses
        ORDER BY id
    ")->fetchAll();
    foreach ($alamatRows as $alamat) {
        $alamatPerPelanggan[$alamat['customer_id']] ??= trim(
            "{$alamat['recipient_name']} ({$alamat['phone']}) — {$alamat['detail']}, {$alamat['city']} {$alamat['postal_code']}"
        );
    }

    $originIds = $pdo->query('SELECT id FROM shipping_origins WHERE is_active = 1 ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
    $courierIds = $pdo->query('SELECT id FROM couriers WHERE is_active = 1 ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
    $ownerId = (int) $pdo->query("SELECT id FROM users WHERE role = 'owner' ORDER BY id LIMIT 1")->fetchColumn();

    $namaPelanggan = [
        'Ayu', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fajar', 'Gita', 'Hendra',
        'Indah', 'Joko', 'Kartika', 'Lukman', 'Maya', 'Nanda', 'Putri', 'Rizky',
        'Sari', 'Tono', 'Umi', 'Vina', 'Wahyu', 'Yanti', 'Zaki', 'Pak Slamet',
    ];

    $pdo->beginTransaction();

    $pdo->exec("DELETE FROM pos_transactions WHERE transaction_number LIKE 'POS-SEED-%'");
    $pdo->exec("DELETE FROM orders WHERE order_number LIKE 'CHKM-SEED-%'");

    $insPos = $pdo->prepare("
        INSERT INTO pos_transactions
            (transaction_number, cashier_id, customer_name, total_amount, payment_method, amount_paid, change_amount, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insPosItem = $pdo->prepare("
        INSERT INTO pos_transaction_items
            (pos_transaction_id, product_size_id, unit_price, quantity, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insPosVariant = $pdo->prepare("
        INSERT INTO pos_item_variants
            (pos_transaction_item_id, variant_id, variant_price)
        VALUES (?, ?, ?)
    ");

    $insOrder = $pdo->prepare("
        INSERT INTO orders
            (order_number, customer_id, delivery_method, shipping_address, shipping_origin_id, courier_id,
             shipping_confirmed_at, shipping_confirmed_by, subtotal, shipping_cost, grand_total,
             status, payment_type, paid_at, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insOrderItem = $pdo->prepare("
        INSERT INTO order_items
            (order_id, product_size_id, unit_price, quantity, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insOrderVariant = $pdo->prepare("
        INSERT INTO order_item_variants
            (order_item_id, variant_id, variant_price)
        VALUES (?, ?, ?)
    ");

    $today = new DateTimeImmutable('today');
    $totalHari = $minggu * 7;
    $totalPos = 0;
    $totalPesanan = 0;
    $totalOmzet = 0.0;

    for ($mundur = $totalHari - 1; $mundur >= 0; $mundur--) {
        $tanggal = $today->modify("-{$mundur} days");
        $akhirPekan = in_array((int) $tanggal->format('N'), [6, 7], true);
        $faktor = 0.75 + (($minggu - 1 - intdiv($mundur, 7)) * 0.05);

        $waktuPos = [];
        $jumlahPos = (int) round(($akhirPekan ? random_int(6, 14) : random_int(4, 10)) * $faktor);
        for ($i = 0; $i < $jumlahPos; $i++) {
            $waktu = riwayat_waktu($tanggal);
            if ($waktu) {
                $waktuPos[] = $waktu;
            }
        }
        usort($waktuPos, fn ($a, $b) => $a <=> $b);

        foreach ($waktuPos as $nomor => $waktu) {
            $status = random_int(1, 100) <= 2 ? 'cancelled' : 'paid';
            $metode = random_int(1, 100) <= 55 ? 'cash' : 'non_cash';
            $jumlahItem = min(count($ukuranList), riwayat_pilih_bobot([1 => 40, 2 => 30, 3 => 20, 4 => 10]));

            $pool = $ukuranList;
            shuffle($pool);
            $items = [];
            $total = 0.0;
            foreach (array_slice($pool, 0, $jumlahItem) as $ukuran) {
                $item = riwayat_item($ukuran, $varianPerProduk);
                $items[] = $item;
                $total += $item['subtotal'];
            }
            $total = round($total, 2);

            if ($metode === 'cash') {
                $kelipatan = random_int(1, 100) <= 70 ? 5000 : 10000;
                $dibayar = ceil($total / $kelipatan) * $kelipatan;
                if ($dibayar === $total && random_int(1, 2) === 1) {
                    $dibayar += $kelipatan;
                }
            } else {
                $dibayar = $total;
            }
            $kembalian = round($dibayar - $total, 2);

            $insPos->execute([
                'POS-SEED-'.$tanggal->format('ymd').'-'.str_pad((string) ($nomor + 1), 2, '0', STR_PAD_LEFT),
                $kasirIds[array_rand($kasirIds)],
                random_int(1, 100) <= 70 ? $namaPelanggan[array_rand($namaPelanggan)] : null,
                $total,
                $metode,
                $dibayar,
                $kembalian,
                $status,
                $waktu->format('Y-m-d H:i:s'),
            ]);
            $posId = (int) $pdo->lastInsertId();

            foreach ($items as $item) {
                $insPosItem->execute([
                    $posId,
                    $item['ukuran']['id'],
                    $item['unit_price'],
                    $item['quantity'],
                    $item['subtotal'],
                ]);
                $itemId = (int) $pdo->lastInsertId();
                foreach ($item['varian'] as $varian) {
                    $insPosVariant->execute([$itemId, $varian['id'], (float) $varian['additional_price']]);
                }
            }

            $totalPos++;
            if ($status === 'paid') {
                $totalOmzet += $total;
            }
        }

        $waktuPesanan = [];
        $jumlahPesanan = (int) round(($akhirPekan ? random_int(2, 6) : random_int(1, 4)) * $faktor);
        for ($i = 0; $i < $jumlahPesanan; $i++) {
            if (random_int(1, 100) <= 15) {
                continue;
            }
            $waktu = riwayat_waktu($tanggal);
            if ($waktu) {
                $waktuPesanan[] = $waktu;
            }
        }
        usort($waktuPesanan, fn ($a, $b) => $a <=> $b);

        foreach ($waktuPesanan as $nomor => $waktu) {
            $umur = (int) $today->diff($tanggal)->days;
            $status = riwayat_status_pesanan($umur);
            $metodeKirim = random_int(1, 100) <= 70 ? 'courier' : 'pickup';
            $ongkir = $metodeKirim === 'courier' ? 5000.0 : 0.0;
            $pelangganId = (int) $pelangganIds[array_rand($pelangganIds)];
            $alamat = $metodeKirim === 'courier'
                ? ($alamatPerPelanggan[$pelangganId] ?? 'Pelanggan Chikumi (0812-0000-0000) — Jl. Raya Bangkalan, Bangkalan 69112')
                : null;

            $jumlahItem = min(count($ukuranList), riwayat_pilih_bobot([1 => 45, 2 => 35, 3 => 20]));
            $pool = $ukuranList;
            shuffle($pool);
            $items = [];
            $subtotal = 0.0;
            foreach (array_slice($pool, 0, $jumlahItem) as $ukuran) {
                $item = riwayat_item($ukuran, $varianPerProduk);
                $items[] = $item;
                $subtotal += $item['subtotal'];
            }
            $subtotal = round($subtotal, 2);
            $grandTotal = round($subtotal + $ongkir, 2);

            $sudahBayar = in_array($status, ['processing', 'ready', 'completed'], true);
            $dibayarPada = null;
            if ($sudahBayar) {
                $dibayarPada = $waktu->modify('+'.random_int(1, 45).' minutes');
                $sekarang = new DateTimeImmutable();
                if ($dibayarPada > $sekarang) {
                    $dibayarPada = $sekarang;
                }
            }

            $konfirmasiPada = null;
            $originId = null;
            $courierId = null;
            $konfirmasiOleh = null;
            if ($metodeKirim === 'courier' && in_array($status, ['ready', 'completed'], true) && random_int(1, 100) <= 80) {
                $konfirmasiPada = $waktu->modify('+'.random_int(10, 90).' minutes');
                $sekarang = new DateTimeImmutable();
                if ($konfirmasiPada > $sekarang) {
                    $konfirmasiPada = $sekarang;
                }
                $originId = $originIds ? $originIds[array_rand($originIds)] : null;
                $courierId = $courierIds ? $courierIds[array_rand($courierIds)] : null;
                $konfirmasiOleh = $ownerId ?: null;
            }

            $insOrder->execute([
                'CHKM-SEED-'.$tanggal->format('ymd').'-'.str_pad((string) ($nomor + 1), 2, '0', STR_PAD_LEFT),
                $pelangganId,
                $metodeKirim,
                $alamat,
                $originId,
                $courierId,
                $konfirmasiPada?->format('Y-m-d H:i:s'),
                $konfirmasiOleh,
                $subtotal,
                $ongkir,
                $grandTotal,
                $status,
                random_int(1, 2) === 1 ? 'qris' : 'va',
                $dibayarPada?->format('Y-m-d H:i:s'),
                $waktu->format('Y-m-d H:i:s'),
                ($dibayarPada ?? $waktu)->format('Y-m-d H:i:s'),
            ]);
            $orderId = (int) $pdo->lastInsertId();

            foreach ($items as $item) {
                $insOrderItem->execute([
                    $orderId,
                    $item['ukuran']['id'],
                    $item['unit_price'],
                    $item['quantity'],
                    $item['subtotal'],
                ]);
                $itemId = (int) $pdo->lastInsertId();
                foreach ($item['varian'] as $varian) {
                    $insOrderVariant->execute([$itemId, $varian['id'], (float) $varian['additional_price']]);
                }
            }

            $totalPesanan++;
            if ($sudahBayar) {
                $totalOmzet += $grandTotal;
            }
        }
    }

    $pdo->commit();

    echo "Seeder riwayat penjualan {$minggu} minggu berhasil: {$totalPos} transaksi kasir, {$totalPesanan} pesanan online, total omzet Rp ".number_format($totalOmzet, 0, ',', '.').'.';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'Seeding riwayat penjualan gagal: '.$e->getMessage();
}
