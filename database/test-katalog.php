<?php
// database/test-katalog.php — smoke test backend area pelanggan.
// Jalankan server dev (php artisan serve) lalu: php database/test-katalog.php [base_url]
// Exit 0 = semua lulus.
$base = getenv('TEST_BASE') ?: ($argv[1] ?? 'http://127.0.0.1:8123');
$devCustomer = getenv('TEST_DEV_CUSTOMER') ?: ($argv[2] ?? '3');
$fail = 0;

function req(string $method, string $path, ?array $body = null, string|bool|null $dev = null): array
{
    global $base, $devCustomer;
    $ch = curl_init($base . $path);
    $headers = ['Accept: application/json'];
    if ($dev !== false) {
        $headers[] = 'X-Dev-Customer: ' . ($dev ?? $devCustomer);
    }
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
    }
    // PHP curl di sini membuang HTTPHEADER kalau POSTFIELDS diset lebih dulu.
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $out = curl_exec($ch);
    if ($out === false) {
        fwrite(STDERR, "Server tidak bisa dihubungi ($base). Jalankan: php artisan serve --port=8123\n");
        exit(1);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, json_decode($out, true) ?? []];
}

function check(string $label, bool $ok, string $extra = ''): void
{
    global $fail;
    echo $ok ? "PASS  $label\n" : "FAIL  $label $extra\n";
    $fail += $ok ? 0 : 1;
}

// katalog
[$s, $j] = req('GET', '/frontend/customer/api/katalog');
$produk = $j['data']['produk'] ?? [];
check('katalog 200 + ada produk', $s === 200 && $produk, "status=$s");
$pertama = $produk[0] ?? [];
check('katalog punya ukuran+varian', isset($pertama['ukuran'][0]['harga_dasar'], $pertama['varian'][0]['pilihan'][0]['tambahan_harga']));
[$s] = req('GET', '/frontend/customer/api/katalog?category=999');
check('katalog kategori tak dikenal -> 422', $s === 422, "status=$s");

// detail produk
[$s, $j] = req('GET', '/frontend/customer/api/produk/' . ($pertama['id'] ?? 1));
check('detail produk 200', $s === 200 && ($j['data']['id'] ?? null) === ($pertama['id'] ?? null), "status=$s");

// alamat
[$s] = req('POST', '/frontend/customer/api/alamat', [
    'recipient_name' => 'Smoke Test', 'phone' => '0800', 'detail' => 'Jl. Uji', 'city' => 'Bangkalan',
]);
check('alamat buat -> 201', $s === 201, "status=$s");

// keranjang
[$s, $j] = req('GET', '/frontend/customer/api/keranjang');
foreach ($j['data']['item'] ?? [] as $i) {
    req('DELETE', "/frontend/customer/api/keranjang/{$i['id']}");
}
$sizeId = $pertama['ukuran'][0]['id'] ?? 1;
$varSatu = $pertama['varian'][0]['pilihan'][0]['id'] ?? null;
$varLainTipe = $pertama['varian'][1]['pilihan'][0]['id'] ?? null;
[$s] = req('POST', '/frontend/customer/api/keranjang', ['product_size_id' => $sizeId, 'quantity' => 1, 'variant_ids' => array_filter([$varSatu, $varLainTipe])]);
check('keranjang tambah -> 201', $s === 201, "status=$s");
[$s] = req('POST', '/frontend/customer/api/keranjang', ['product_size_id' => $sizeId, 'quantity' => 1, 'variant_ids' => array_fill(0, 2, $varSatu)]);
check('varian dobel -> 422', $s === 422, "status=$s");
[$s] = req('GET', '/frontend/customer/api/keranjang', null, false);
check('keranjang tanpa login (dev) -> 403', $s === 403, "status=$s");

// cleanup keranjang
[$s, $j] = req('GET', '/frontend/customer/api/keranjang');
foreach ($j['data']['item'] ?? [] as $i) {
    req('DELETE', "/frontend/customer/api/keranjang/{$i['id']}");
}

echo $fail ? "\n$fail pemeriksaan GAGAL\n" : "\nSemua pemeriksaan lulus\n";
exit($fail ? 1 : 0);
