@php
    $apiUrl = auth()->check() ? '/kasir/api/transaksi' : '/frontend/kasir/api/transaksi';
    $devKasirId = auth()->check() ? null : 2;
    $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
    $panjang = fn ($nama) => $nama === 'Regular' ? '16 cm' : '25 cm';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Kasir - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body * { visibility: hidden; }
            #kertasStruk, #kertasStruk * { visibility: visible; }
            #kertasStruk { position: fixed; left: 0; top: 0; width: 302px; box-shadow: none; }
        }
    </style>
</head>
<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">
    <!-- TOPBAR KASIR -->
    <header class="sticky top-0 z-30 flex h-[76px] items-center gap-6 border-b border-[#EDE6DF] bg-white px-7">
        <div class="shrink-0">
            <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
            <p class="text-[10px] font-semibold tracking-wide">JAPANESE SNACK</p>
        </div>
        <!-- MENU KASIR -->
        <nav class="flex items-center gap-1 rounded-xl bg-[#F7F3EF] p-1">
            <a href="#" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                POS
            </a>
            <a href="#" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Riwayat
            </a>
        </nav>
        <div class="ml-auto flex items-center gap-4">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
            </span>
            <div class="leading-tight">
                <p class="text-sm font-semibold">{{ auth()->user()->name ?? 'Kasir' }}</p>
                <p class="text-xs text-[#6B645D]">Kasir</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" aria-label="Keluar"
                    class="grid h-11 w-11 place-items-center rounded-xl border border-[#EDE6DF] bg-white text-[#E03131] transition hover:bg-[#FDECEC]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                        stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <div class="lg:grid lg:min-h-[calc(100vh-76px)] lg:grid-cols-[minmax(0,1fr)_430px]">

        <main class="space-y-5 p-7">

            <div>
                <h2 class="text-[26px] font-bold">Pilih Ukuran</h2>
                <p class="mt-0.5 text-sm text-[#6B645D]">Ketuk ukuran, lalu pilih varian pesanan</p>
            </div>

            <!-- KARTU UKURAN -->
            <div class="grid gap-5 md:grid-cols-2">
                @forelse ($produk as $p)
                    @foreach ($p['ukuran'] as $u)
                        @php
                            $habis = $u['stok'] <= 0;
                            $menipis = ! $habis && $u['stok'] <= $u['min_stok'];
                            $chip = [['label' => 'Harga dasar', 'harga' => $u['harga']]];
                            if ($u['harga_seasoning'] !== null) {
                                $chip[] = ['label' => '+ Seasoning', 'harga' => $u['harga_seasoning']];
                            }
                            if ($u['harga_dip'] !== null) {
                                $chip[] = ['label' => '+ Dip Sauce', 'harga' => $u['harga_dip']];
                            }
                        @endphp
                        <button type="button" data-produk-id="{{ $p['id'] }}" data-ukuran-id="{{ $u['id'] }}"
                            @disabled($habis)
                            class="{{ $habis ? 'cursor-not-allowed rounded-2xl border-2 border-transparent bg-white/60 p-[18px] text-left' : 'block rounded-2xl border-2 border-transparent bg-white p-[18px] text-left outline-none transition hover:border-[#FF8000] focus-visible:border-[#FF8000]' }}">
                            <div class="grid h-[180px] place-items-center overflow-hidden rounded-xl {{ $habis ? 'bg-[#FFE0BF]/60' : 'bg-[#FF8000]' }}">
                                <img src="{{ $p['gambar'] }}" alt="{{ $p['nama'] }}"
                                    class="h-[176px] w-auto object-contain {{ $habis ? 'opacity-50' : '' }}">
                            </div>

                            <div class="mt-3.5 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[22px] font-extrabold italic leading-tight {{ $habis ? 'text-[#A39B93]' : '' }}">{{ $p['nama'] }}</p>
                                    <p class="text-sm {{ $habis ? 'text-[#A39B93]' : 'text-[#6B645D]' }}">{{ $u['nama'] }} · {{ $panjang($u['nama']) }}</p>
                                </div>
                                @if ($habis)
                                    <span class="mt-3 inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-xs font-medium text-[#E03131]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span> Habis
                                    </span>
                                @elseif ($menipis)
                                    <span class="mt-3 inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#FFF4D6] px-2.5 py-1 text-xs font-medium text-[#C27C00]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#C27C00]"></span> Sisa {{ $u['stok'] }} pcs · menipis
                                    </span>
                                @else
                                    <span class="mt-3 inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#E8F7EC] px-2.5 py-1 text-xs font-medium text-[#2F9E44]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-[#2F9E44]"></span> Sisa {{ $u['stok'] }} pcs
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3.5 grid grid-cols-2 gap-2.5">
                                @foreach ($chip as $c)
                                    <div class="rounded-xl {{ $habis ? 'bg-[#FFF9F2]' : 'bg-[#FFF3E6]' }} p-3">
                                        <p class="text-xs {{ $habis ? 'text-[#A39B93]/70' : 'text-[#6B645D]' }}">{{ $c['label'] }}</p>
                                        <p class="mt-0.5 text-lg font-bold {{ $habis ? 'text-[#A39B93]/70' : 'text-[#D96C00]' }}">{{ $rupiah($c['harga']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </button>
                    @endforeach
                @empty
                    <p class="text-sm text-[#6B645D]">Belum ada produk aktif.</p>
                @endforelse
            </div>

            <!-- PILIHAN RASA HARI INI -->
            <section class="rounded-2xl border border-[#EDE6DF] bg-white p-[22px]">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base font-semibold">Pilihan Rasa Hari Ini</h3>
                    <p class="text-xs text-[#6B645D]">Harga tidak berubah oleh filling</p>
                </div>

                <div class="mt-3.5 space-y-3.5">
                    @foreach ($rasa as $group)
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <div class="w-[140px] shrink-0">
                                <p class="text-sm font-semibold">{{ $group['judul'] }}</p>
                                <p class="text-xs text-[#6B645D]">{{ $group['aturan'] }}</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($group['pilihan'] as $pilihan)
                                    @if ($pilihan['tersedia'])
                                        <span class="rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm">{{ $pilihan['nama'] }}</span>
                                    @else
                                        <span class="rounded-full border border-[#F7F3EF] bg-[#FFF9F2] px-5 py-2.5 text-sm text-[#A39B93]/70">{{ $pilihan['nama'] }} · habis</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        <aside class="flex flex-col border-t border-[#EDE6DF] bg-white p-6 lg:sticky lg:top-[76px] lg:h-[calc(100vh-76px)] lg:border-l lg:border-t-0">

            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold">Pesanan Baru</h2>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#D96C00]"></span> <span id="jumlahItem">0 item</span>
                </span>
            </div>

            <label for="namaPelanggan" class="mt-4 block text-sm font-medium">Nama Pelanggan (opsional)</label>
            <div class="mt-2 flex items-center gap-3 rounded-xl border border-[#EDE6DF] px-4 py-3.5 focus-within:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5 shrink-0 text-[#A39B93]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <input id="namaPelanggan" type="text" placeholder="Contoh: Budi"
                    class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
            </div>

            <!-- DAFTAR ITEM -->
            <div id="daftarItem" class="mt-3.5 flex-1 space-y-3.5 overflow-y-auto">
                <p class="mt-6 text-center text-sm text-[#A39B93]">Belum ada item. Pilih ukuran di kiri.</p>
            </div>

            <!-- TOTAL -->
            <div class="mt-4 border-t border-[#EDE6DF] pt-3.5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#6B645D]" id="subtotalQty">Subtotal (0 pcs)</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div class="mt-3.5 flex items-center justify-between">
                    <span class="text-lg font-bold">Total</span>
                    <span id="total" class="text-[26px] font-bold text-[#D96C00]">Rp 0</span>
                </div>

                <div class="mt-3.5 grid grid-cols-2 gap-2.5">
                    <label class="block">
                        <span class="mb-1 block text-xs text-[#6B645D]">Metode</span>
                        <select id="metodeBayar"
                            class="w-full rounded-xl border border-[#EDE6DF] bg-white px-3 py-2.5 text-sm font-semibold outline-none focus:border-[#FF8000]">
                            <option value="cash">Tunai</option>
                            <option value="non_cash">Non-tunai</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs text-[#6B645D]">Uang dibayar</span>
                        <input id="jumlahBayar" type="number" min="0" placeholder="0"
                            class="w-full rounded-xl border border-[#EDE6DF] bg-white px-3 py-2.5 text-sm font-semibold outline-none focus:border-[#FF8000]">
                    </label>
                </div>
                <p id="kembalian" class="mt-1.5 text-xs font-medium text-[#2F9E44]"></p>

                <div class="mt-3.5 flex gap-2.5">
                    <button type="button" id="btnBatal"
                        class="w-[102px] rounded-xl border border-[#EDE6DF] bg-white py-4 text-base font-semibold transition hover:border-[#A39B93]">
                        Batal
                    </button>
                    <button type="button" id="btnBayar"
                        class="flex-1 rounded-xl bg-[#FF8000] py-4 text-base font-semibold text-white transition hover:bg-[#D96C00]">
                        <span id="labelBayar">Bayar · Rp 0</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- POPUP K2 · PILIH VARIAN (form transaksi kasir) -->
    @include('components.kasir.pilih-varian')
    @include('components.kasir.pembayaran')
    @include('components.kasir.struk')

    <script>
        const PRODUK = @json($produk);
        const API_URL = @json($apiUrl);
        const DEV_KASIR = @json($devKasirId);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const rp = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
        const esc = s => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));
        const el = id => document.getElementById(id);
        const LABEL = {filling: 'Creamy Filling', dipping_sauce: 'Dip Sauce', seasoning: 'Seasoning'};
        const ATURAN = {filling: 'wajib pilih 1', dipping_sauce: 'pilih 1', seasoning: 'boleh lebih dari 1'};

        let produkAktif = null;
        let ukuranAktif = null;
        let keranjang = [];

        function cari(produkId, ukuranId) {
            const p = PRODUK.find(x => x.id === produkId);
            const u = p?.ukuran.find(x => x.id === ukuranId);
            return p && u ? {p, u} : null;
        }

        function bukaVarian(produkId, ukuranId) {
            const found = cari(produkId, ukuranId);
            if (!found) return;
            produkAktif = found.p;
            ukuranAktif = found.u;
            el('varianJudul').textContent = `${found.p.nama} · ${found.u.nama}`;
            el('varianGambar').src = found.p.gambar;
            el('bagianVarian').innerHTML = found.p.varian.map(g => {
                const tunggal = g.type !== 'seasoning';
                const opsi = g.pilihan.map(v => `
                    <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white ${v.tersedia ? '' : 'cursor-not-allowed bg-[#FFF9F2] text-[#A39B93]/70'}">
                        <input type="${tunggal ? 'radio' : 'checkbox'}" name="varian_${g.type}" value="${v.id}"
                            data-id="${v.id}" data-nama="${esc(v.nama)}" data-harga="${v.harga}" data-type="${g.type}"
                            class="sr-only" ${v.tersedia ? '' : 'disabled'}>
                        ${esc(v.nama)}${v.harga ? ' · +' + rp(v.harga) : ''}${v.tersedia ? '' : ' · habis'}
                    </label>`).join('');
                return `<section class="mt-5">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold">${LABEL[g.type] || g.type}</h3>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-[11px] font-medium text-[#6B645D]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B645D]"></span> ${ATURAN[g.type] || ''}
                        </span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">${opsi}</div>
                </section>`;
            }).join('');
            el('kasirJumlah').value = 1;
            perbaruiHargaVarian();
            el('pilih-varian').classList.replace('hidden', 'flex');
        }

        function tutupVarian() {
            const modal = el('pilih-varian');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function pilihan() {
            return [...document.querySelectorAll('#bagianVarian input:checked')].map(input => ({
                id: Number(input.dataset.id),
                nama: input.dataset.nama,
                harga: Number(input.dataset.harga),
                type: input.dataset.type,
            }));
        }

        function hitungVarian() {
            if (!ukuranAktif) return { harga: 0, qty: 1, total: 0 };
            const harga = ukuranAktif.harga + pilihan().reduce((t, v) => t + v.harga, 0);
            const qty = Math.min(99, Math.max(1, parseInt(el('kasirJumlah').value || '1', 10) || 1));
            return { harga, qty, total: harga * qty };
        }

        function perbaruiHargaVarian() {
            if (!ukuranAktif) return;
            const { harga, qty, total } = hitungVarian();
            el('rincianPorsi').textContent = qty > 1 ? `${qty} porsi × ${rp(harga)}` : 'Harga per porsi';
            el('hargaPorsi').textContent = rp(total);
        }

        function tambah() {
            if (!produkAktif || !ukuranAktif) return;
            const { harga, qty } = hitungVarian();
            const varian = pilihan();
            const butuhFilling = produkAktif.varian.some(g => g.type === 'filling' && g.pilihan.some(v => v.tersedia));
            if (butuhFilling && !varian.some(v => v.type === 'filling')) {
                alert('Pilih filling dulu.');
                return;
            }
            const key = ukuranAktif.id + '|' + varian.map(v => v.id).sort((a, b) => a - b).join(',');
            const ada = keranjang.find(k => k.key === key);
            if (ada) {
                if (ada.qty + qty > ukuranAktif.stok) {
                    alert(`Stok tersisa ${ukuranAktif.stok}.`);
                    return;
                }
                ada.qty += qty;
            } else {
                if (qty > ukuranAktif.stok) {
                    alert(`Stok tersisa ${ukuranAktif.stok}.`);
                    return;
                }
                keranjang.push({
                    key,
                    ukuran_id: ukuranAktif.id,
                    nama: `${produkAktif.nama} · ${ukuranAktif.nama}`,
                    gambar: produkAktif.gambar,
                    varian,
                    harga,
                    qty,
                    stok: ukuranAktif.stok,
                });
            }
            renderKeranjang();
            tutupVarian();
        }

        function totalKeranjang() {
            return keranjang.reduce((t, k) => t + k.harga * k.qty, 0);
        }

        function renderKeranjang() {
            const daftar = el('daftarItem');
            const total = totalKeranjang();
            const jumlah = keranjang.reduce((t, k) => t + k.qty, 0);
            el('jumlahItem').textContent = `${jumlah} item`;
            el('subtotalQty').textContent = `Subtotal (${jumlah} pcs)`;
            el('subtotal').textContent = rp(total);
            el('total').textContent = rp(total);
            el('labelBayar').textContent = 'Bayar · ' + rp(total);
            if (!keranjang.length) {
                daftar.innerHTML = '<p class="mt-6 text-center text-sm text-[#A39B93]">Belum ada item. Pilih ukuran di kiri.</p>';
                el('jumlahBayar').value = '';
                el('kembalian').textContent = '';
                return;
            }
            daftar.innerHTML = keranjang.map(k => `
                <div class="flex gap-3 rounded-2xl bg-[#F7F3EF] p-3">
                    <div class="grid h-[52px] w-[52px] shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="${k.gambar}" alt="" class="h-12 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold">${esc(k.nama)}</p>
                            <button type="button" data-hapus="${k.key}" aria-label="Hapus" class="text-[#E03131] hover:opacity-70">✕</button>
                        </div>
                        <p class="mt-1 text-xs text-[#6B645D]">${k.varian.map(v => esc(v.nama)).join(' · ') || 'Tanpa varian'}</p>
                        <div class="mt-2 flex items-center justify-between">
                            <div class="flex items-center rounded-lg border border-[#EDE6DF] bg-white">
                                <button type="button" data-kurang="${k.key}" aria-label="Kurangi" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">−</button>
                                <span class="w-[34px] text-center text-sm font-semibold">${k.qty}</span>
                                <button type="button" data-tambah="${k.key}" aria-label="Tambah" class="grid h-[34px] w-[34px] place-items-center text-[#FF8000]">+</button>
                            </div>
                            <p class="text-sm font-bold text-[#D96C00]">${rp(k.harga * k.qty)}</p>
                        </div>
                    </div>
                </div>`).join('');
            perbaruiKembalian();
        }

        function perbaruiKembalian() {
            const total = totalKeranjang();
            const metode = el('metodeBayar').value;
            const bayarEl = el('jumlahBayar');
            if (metode === 'non_cash') {
                bayarEl.value = total;
                bayarEl.readOnly = true;
            } else {
                bayarEl.readOnly = false;
            }
            const bayar = Number(bayarEl.value || 0);
            el('kembalian').textContent = bayar ? 'Kembalian: ' + rp(Math.max(0, bayar - total)) : '';
        }

        const LABEL_METODE = {cash: 'Tunai', non_cash: 'Non-tunai'};
        const angka = nilai => Number(String(nilai ?? '').replace(/\D/g, '') || 0);
        const formatAngka = nilai => new Intl.NumberFormat('id-ID').format(angka(nilai));

        let transaksiTerakhir = null;

        function metodeBayar() {
            return document.querySelector('#pembayaran input[name="payment_method"]:checked')?.value || 'cash';
        }

        function waktuSekarang() {
            return new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}).replace('.', ':') + ' WIB';
        }

        function barisRingkasan(nama, gambar, varian, qty, subtotal) {
            return `
                <div class="flex items-center gap-3 rounded-2xl bg-[#F7F3EF] p-2.5">
                    <div class="grid h-12 w-12 shrink-0 place-items-center overflow-hidden rounded-xl bg-[#FF8000]">
                        <img src="${esc(gambar || '')}" alt="" class="h-11 w-auto object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold">${esc(nama)}</p>
                        <p class="truncate text-xs text-[#6B645D]">${esc(varian || 'Tanpa varian')}</p>
                    </div>
                    <span class="text-sm text-[#6B645D]">${qty}×</span>
                    <span class="w-[72px] text-right text-sm font-semibold">${rp(subtotal)}</span>
                </div>`;
        }

        function isiRingkasan(containerId, totalId, qtyId, subtotalId, baris, total, jumlah) {
            el(containerId).innerHTML = baris;
            el(qtyId).textContent = `Subtotal (${jumlah} pcs)`;
            el(subtotalId).textContent = rp(total);
            el(totalId).textContent = rp(total);
        }

        function aturNominalCepat(total) {
            [
                ['pas', total],
                ['tengah', Math.ceil(total / 50000) * 50000],
                ['penuh', Math.ceil(total / 100000) * 100000],
            ].forEach(([kunci, nominal]) => {
                const input = document.querySelector(`input[name="uang_cepat"][data-quick="${kunci}"]`);
                if (!input) return;
                input.value = nominal;
                const label = document.querySelector(`[data-quick-label="${kunci}"]`);
                if (label) label.textContent = kunci === 'pas' ? 'Uang Pas' : rp(nominal);
            });
        }

        function perbaruiKembalianBayar() {
            const total = totalKeranjang();
            const metode = metodeBayar();
            const input = el('uangDiterima');
            input.readOnly = metode === 'non_cash';
            if (metode === 'non_cash') input.value = formatAngka(total);
            const bayar = angka(input.value);
            el('kembalianTunai').textContent = rp(Math.max(0, bayar - total));
        }

        function bukaPembayaran() {
            if (!keranjang.length) {
                alert('Pesanan masih kosong.');
                return;
            }
            const total = totalKeranjang();
            const jumlah = keranjang.reduce((t, k) => t + k.qty, 0);

            el('bayarNomor').textContent = '—';
            el('bayarWaktu').textContent = waktuSekarang();
            isiRingkasan(
                'ringkasanItem', 'ringkasanTotal', 'ringkasanSubtotalQty', 'ringkasanSubtotal',
                keranjang.map(k => barisRingkasan(
                    k.nama, k.gambar,
                    k.varian.map(v => v.nama).join(' · '),
                    k.qty, k.harga * k.qty
                )).join(''),
                total, jumlah
            );
            el('qrisTotal').textContent = rp(total);
            el('transferTotal').textContent = rp(total);
            document.querySelectorAll('[data-total-text]').forEach(node => node.textContent = rp(total));

            const metode = el('metodeBayar').value;
            const radio = document.querySelector(`#pembayaran input[name="payment_method"][value="${metode}"]`);
            if (radio) radio.checked = true;

            aturNominalCepat(total);
            const awal = el('jumlahBayar').value;
            el('uangDiterima').value = awal !== '' ? formatAngka(awal) : formatAngka(total);
            perbaruiKembalianBayar();

            location.hash = '#pembayaran';
        }

        function tutupPembayaran() {
            location.hash = '';
        }

        async function prosesPembayaran() {
            if (!keranjang.length) {
                alert('Pesanan masih kosong.');
                return;
            }

            const total = totalKeranjang();
            const metode = metodeBayar();
            const dibayar = metode === 'non_cash' ? total : angka(el('uangDiterima').value);

            if (dibayar < total) {
                alert('Uang dibayar kurang dari total.');
                return;
            }

            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };
            if (CSRF) headers['X-CSRF-TOKEN'] = CSRF;
            if (DEV_KASIR) headers['X-Dev-Kasir'] = DEV_KASIR;

            try {
                el('btnBayar').disabled = true;
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        payment_method: metode,
                        amount_paid: dibayar,
                        customer_name: el('namaPelanggan').value.trim() || null,
                        items: keranjang.map(k => ({
                            product_size_id: k.ukuran_id,
                            quantity: k.qty,
                            variant_ids: k.varian.map(v => v.id),
                        })),
                    }),
                });
                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(hasil.message || 'Gagal menyimpan transaksi.');
                }

                transaksiTerakhir = hasil.data;
                tampilkanBerhasil(hasil.data);
                isiStruk(hasil.data);
                keranjang = [];
                renderKeranjang();
                location.hash = '#pembayaran-berhasil';
            } catch (error) {
                alert(error.message);
            } finally {
                el('btnBayar').disabled = false;
            }
        }

        function tampilkanBerhasil(data) {
            const jumlah = data.item.reduce((t, item) => t + item.quantity, 0);
            el('berhasilNomorHeader').textContent = data.nomor;
            el('berhasilWaktuHeader').textContent = data.tanggal;
            isiRingkasan(
                'ringkasanBerhasil', 'berhasilGrandTotal', 'berhasilSubtotalQty', 'berhasilSubtotal',
                data.item.map(item => barisRingkasan(
                    `${item.produk} · ${item.ukuran}`,
                    item.gambar,
                    item.varian.map(v => v.nama).join(' · '),
                    item.quantity, item.subtotal
                )).join(''),
                data.total, jumlah
            );
            el('berhasilMetode').textContent = LABEL_METODE[data.metode_pembayaran] || data.metode_pembayaran;
            el('berhasilTotal').textContent = rp(data.total);
            el('berhasilWaktu').textContent = data.tanggal;
            el('berhasilNomor').textContent = data.nomor;
            el('berhasilRef').textContent = data.nomor;
            el('berhasilStatus').textContent = data.status === 'paid' ? 'Lunas' : data.status;
            document.querySelectorAll('[data-metode-chip]').forEach(chip => {
                const aktif = chip.dataset.metodeChip === data.metode_pembayaran;
                chip.classList.toggle('border-[#FF8000]', aktif);
                chip.classList.toggle('bg-[#FF8000]', aktif);
                chip.classList.toggle('text-white', aktif);
            });
        }

        function isiStruk(data) {
            const jumlah = data.item.reduce((t, item) => t + item.quantity, 0);
            el('strukNomor').textContent = data.nomor;
            el('strukTanggal').textContent = data.tanggal;
            el('strukKasir').textContent = data.kasir || '-';
            el('strukPelanggan').textContent = data.pelanggan || '-';
            el('strukItem').innerHTML = data.item.map(item => `
                <div>
                    <p class="font-bold">${esc(item.produk)} · ${esc(item.ukuran)}</p>
                    <p class="text-[11px] text-[#6B645D]">${esc(item.varian.map(v => v.nama).join(' · ') || 'Tanpa varian')}</p>
                    <div class="flex justify-between">
                        <span>${item.quantity} x ${formatAngka(item.harga_satuan)}</span>
                        <span>${formatAngka(item.subtotal)}</span>
                    </div>
                </div>`).join('');
            el('strukSubtotalQty').textContent = `Subtotal (${jumlah} pcs)`;
            el('strukSubtotal').textContent = formatAngka(data.total);
            el('strukTotal').textContent = rp(data.total);
            el('strukMetode').textContent = LABEL_METODE[data.metode_pembayaran] || data.metode_pembayaran;
            el('strukDibayar').textContent = formatAngka(data.dibayar);
            el('strukKembalian').textContent = formatAngka(data.kembalian);
            el('strukStatus').textContent = data.status === 'paid' ? 'LUNAS' : String(data.status).toUpperCase();
        }

        function cetakStruk() {
            if (!transaksiTerakhir) {
                alert('Belum ada transaksi untuk dicetak.');
                return;
            }
            window.print();
        }

        function transaksiBaru() {
            transaksiTerakhir = null;
            keranjang = [];
            renderKeranjang();
            location.hash = '';
        }

        document.querySelectorAll('[data-produk-id]').forEach(button => {
            button.addEventListener('click', () => bukaVarian(Number(button.dataset.produkId), Number(button.dataset.ukuranId)));
        });
        el('bagianVarian').addEventListener('change', perbaruiHargaVarian);
        el('kasirJumlah').addEventListener('input', perbaruiHargaVarian);
        el('daftarItem').addEventListener('click', event => {
            const button = event.target.closest('button');
            if (!button) return;
            const key = button.dataset.hapus ?? button.dataset.kurang ?? button.dataset.tambah;
            const item = keranjang.find(k => k.key === key);
            if (!item) return;
            if (button.dataset.hapus) {
                keranjang = keranjang.filter(k => k.key !== key);
            } else if (button.dataset.kurang) {
                item.qty--;
                if (item.qty < 1) keranjang = keranjang.filter(k => k.key !== key);
            } else {
                if (item.qty + 1 > item.stok) {
                    alert(`Stok tersisa ${item.stok}.`);
                    return;
                }
                item.qty++;
            }
            renderKeranjang();
        });
        el('metodeBayar').addEventListener('change', perbaruiKembalian);
        el('jumlahBayar').addEventListener('input', perbaruiKembalian);
        el('btnBatal').addEventListener('click', () => {
            keranjang = [];
            renderKeranjang();
        });
        el('btnBayar').addEventListener('click', bukaPembayaran);

        // Layar pembayaran (tunai / QRIS / transfer)
        document.querySelectorAll('#pembayaran input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', perbaruiKembalianBayar);
        });
        el('uangDiterima').addEventListener('input', perbaruiKembalianBayar);
        document.querySelectorAll('#pembayaran input[name="uang_cepat"]').forEach(input => {
            input.addEventListener('change', () => {
                el('uangDiterima').value = formatAngka(input.value);
                perbaruiKembalianBayar();
            });
        });
        document.querySelectorAll('#pembayaran [data-key]').forEach(button => {
            button.addEventListener('click', () => {
                if (metodeBayar() !== 'cash') return;
                const sekarang = String(angka(el('uangDiterima').value));
                const kunci = button.dataset.key;
                const berikut = kunci === 'del'
                    ? sekarang.slice(0, -1)
                    : (sekarang + kunci).replace(/^0+(?=\d)/, '');
                el('uangDiterima').value = formatAngka(berikut);
                perbaruiKembalianBayar();
            });
        });

        renderKeranjang();
    </script>
</body>

</html>
