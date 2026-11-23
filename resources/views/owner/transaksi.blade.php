@php
    $apiBase = auth()->check() ? '/owner/api' : '/frontend/owner/api';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Transaksi - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <div class="flex min-h-screen">

        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

        @include('components.owner.sidebar')

        <main class="group min-w-0 flex-1">

            @include('components.owner.mobile-header')

            <div class="mx-auto max-w-[1500px] space-y-5 p-4 sm:p-6 lg:p-8">

                <!-- JUDUL -->
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[28px] font-bold">Transaksi</h2>
                        <p class="mt-1 text-sm text-[#6B645D]">Uang masuk dari penjualan dan pengeluaran usaha</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="#" id="btnExport"
                            class="rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white px-7 py-[15px] text-base font-semibold transition hover:border-[#FF8000]">Export</a>
                        <a href="#catat-pengeluaran"
                            class="rounded-[14px] bg-[#FF8000] px-7 py-[15px] text-base font-semibold text-white transition hover:bg-[#D96C00]">+ Catat Pengeluaran</a>
                    </div>
                </div>

                <!-- FILTER -->
                <div class="flex flex-wrap items-center gap-2">
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-semua" value="semua" class="sr-only" checked>Semua
                    </label>
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-masuk" value="masuk" class="sr-only">Uang Masuk
                    </label>
                    <label class="cursor-pointer rounded-full border-[1.5px] border-[#EDE6DF] bg-white px-5 py-2.5 text-sm font-medium transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:text-white">
                        <input type="radio" name="jenis" id="jenis-keluar" value="keluar" class="sr-only">Pengeluaran
                    </label>
                    <select name="periode" class="ml-auto rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 py-2.5 text-[13px] font-medium outline-none focus:border-[#FF8000]">
                        @php
                            $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        @endphp
                        @for ($i = 0; $i < 3; $i++)
                            @php $bulan = now()->startOfMonth()->subMonths($i); @endphp
                            <option value="{{ $bulan->format('Y-m') }}" @selected($i === 0)>
                                {{ $namaBulan[(int) $bulan->month] }} {{ $bulan->year }}
                            </option>
                        @endfor
                    </select>
                    <select name="kategori" class="rounded-xl border-[1.5px] border-[#EDE6DF] bg-white px-4 py-2.5 text-[13px] font-medium outline-none focus:border-[#FF8000]">
                        <option value="">Semua kategori</option>
                        <option value="penjualan_kasir">Penjualan Kasir</option>
                        <option value="penjualan_online">Penjualan Online</option>
                        <option value="raw_material">Bahan Baku</option>
                        <option value="packaging">Kemasan</option>
                        <option value="operational">Operasional</option>
                        <option value="salary">Gaji</option>
                        <option value="bonus">Bonus</option>
                        <option value="overtime">Lembur</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <!-- RIWAYAT TRANSAKSI: diisi dari GET /owner/api/transaksi -->
                <section class="overflow-hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white">
                    <div class="px-5 py-4">
                        <h3 class="text-base font-semibold">Riwayat Transaksi · <span id="judulPeriode">-</span></h3>
                        <p class="mt-0.5 text-xs text-[#6B645D]">Penjualan tercatat otomatis, pengeluaran dicatat manual</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[860px] text-left text-[13px]">
                            <thead class="bg-[#F7F3EF] text-[11px] font-semibold text-[#6B645D]">
                                <tr>
                                    <th class="px-5 py-2.5">WAKTU</th>
                                    <th class="px-5 py-2.5">JENIS</th>
                                    <th class="px-5 py-2.5">KATEGORI</th>
                                    <th class="px-5 py-2.5">KETERANGAN</th>
                                    <th class="px-5 py-2.5">METODE</th>
                                    <th class="px-5 py-2.5">NOMINAL</th>
                                    <th class="px-5 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody id="tabelTransaksi" class="divide-y divide-[#EDE6DF]"></tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#EDE6DF] px-5 py-3">
                        <p id="infoHalaman" class="text-xs text-[#6B645D]">Memuat transaksi...</p>
                        <div id="pager" class="flex gap-1.5"></div>
                    </div>
                </section>

                <!-- TOTAL PERIODE -->
                <section class="grid gap-4 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-[#6B645D]">Uang masuk (<span id="jumlahMasuk">0</span> transaksi)</p>
                        <p id="totalMasuk" class="mt-1 text-lg font-bold text-[#1F9D55]">+ Rp 0</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B645D]">Pengeluaran (<span id="jumlahKeluar">0</span> transaksi)</p>
                        <p id="totalKeluar" class="mt-1 text-lg font-bold text-[#E03131]">− Rp 0</p>
                    </div>
                    <div>
                        <p class="text-xs text-[#6B645D]">Selisih</p>
                        <p id="totalSelisih" class="mt-1 text-lg font-bold text-[#D96C00]">Rp 0</p>
                    </div>
                </section>

            </div>
        </main>
    </div>

    @include('components.owner.catat-pengeluaran')

    <script>
        const API_BASE = @json($apiBase);
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const formPengeluaran = document.getElementById('formPengeluaran');

        const NAMA_BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const rupiah = nilai => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(Math.abs(nilai)));
        const esc = teks => String(teks ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        let HALAMAN = 1;

        const el = id => document.getElementById(id);
        const periodeTerpilih = () => document.querySelector('select[name="periode"]').value;
        const jenisTerpilih = () => document.querySelector('input[name="jenis"]:checked')?.value || 'semua';
        const kategoriTerpilih = () => document.querySelector('select[name="kategori"]').value;

        function waktuTampil(item) {
            const [tanggal, jam] = item.waktu.split(' ');
            const [tahun, bulan, hari] = tanggal.split('-');
            return jam ? `${hari}/${bulan} ${jam.replace(':', '.')}` : `${hari}/${bulan}`;
        }

        function barisTransaksi(item) {
            const masuk = item.jenis === 'masuk';
            const badge = masuk
                ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-[11px] font-semibold text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Masuk</span>'
                : '<span class="inline-flex items-center gap-1.5 rounded-full bg-[#FDECEC] px-2.5 py-1 text-[11px] font-semibold text-[#E03131]"><span class="h-1.5 w-1.5 rounded-full bg-[#E03131]"></span>Keluar</span>';
            const nominal = masuk
                ? `<span class="font-semibold text-[#1F9D55]">+ ${rupiah(item.nominal)}</span>`
                : `<span class="font-semibold text-[#E03131]">− ${rupiah(item.nominal)}</span>`;
            const aksi = masuk
                ? '<a href="#" aria-label="Lihat struk" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]">🧾</a>'
                : '<a href="#catat-pengeluaran" aria-label="Ubah pengeluaran" class="grid h-8 w-8 place-items-center rounded-lg text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]">✎</a>';

            return `<tr>
                <td class="px-5 py-3 text-xs text-[#6B645D]">${waktuTampil(item)}</td>
                <td class="px-5 py-3">${badge}</td>
                <td class="px-5 py-3">${esc(item.kategori_label)}</td>
                <td class="px-5 py-3">${esc(item.keterangan)}</td>
                <td class="px-5 py-3 text-[#6B645D]">${esc(item.metode_label)}</td>
                <td class="px-5 py-3">${nominal}</td>
                <td class="px-5 py-3">${aksi}</td>
            </tr>`;
        }

        function gambarPager(meta) {
            const tombol = (teks, halaman, aktif = false) =>
                `<button type="button" data-halaman="${halaman}" class="grid h-[34px] w-[34px] place-items-center rounded-[10px] border-[1.5px] text-[13px] font-semibold ${
                    aktif ? 'border-[#FF8000] bg-[#FF8000] text-white' : 'border-[#EDE6DF] bg-white hover:border-[#FF8000]'
                }">${teks}</button>`;

            let html = tombol('‹', Math.max(1, meta.halaman - 1));
            for (let i = 1; i <= meta.total_halaman; i++) {
                if (meta.total_halaman > 7 && Math.abs(i - meta.halaman) > 2 && i !== 1 && i !== meta.total_halaman) {
                    if (i === 2 || i === meta.total_halaman - 1) html += '<span class="px-1 text-[#6B645D]">…</span>';
                    continue;
                }
                html += tombol(i, i, i === meta.halaman);
            }
            html += tombol('›', Math.min(meta.total_halaman, meta.halaman + 1));

            el('pager').innerHTML = html;
            el('pager').querySelectorAll('button').forEach(btn =>
                btn.addEventListener('click', () => { HALAMAN = Number(btn.dataset.halaman); muatTransaksi(); })
            );
        }

        async function muatTransaksi() {
            const params = new URLSearchParams({
                jenis: jenisTerpilih(),
                periode: periodeTerpilih(),
                halaman: HALAMAN,
            });
            if (kategoriTerpilih()) params.set('kategori', kategoriTerpilih());

            try {
                const response = await fetch(`${API_BASE}/transaksi?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) return;

                const { data } = await response.json();
                const [tahun, bulan] = data.periode.split('-');

                el('judulPeriode').textContent = `${NAMA_BULAN[Number(bulan) - 1]} ${tahun}`;
                el('tabelTransaksi').innerHTML = data.transaksi.length
                    ? data.transaksi.map(barisTransaksi).join('')
                    : '<tr><td colspan="7" class="px-5 py-8 text-center text-sm text-[#6B645D]">Belum ada transaksi pada filter ini.</td></tr>';

                const awal = (data.meta.halaman - 1) * data.meta.per_halaman + 1;
                const akhir = Math.min(data.meta.halaman * data.meta.per_halaman, data.meta.total);
                el('infoHalaman').textContent = data.meta.total
                    ? `Menampilkan ${awal}–${akhir} dari ${data.meta.total} transaksi`
                    : 'Tidak ada transaksi';
                gambarPager(data.meta);

                el('jumlahMasuk').textContent = data.ringkasan.jumlah_masuk;
                el('jumlahKeluar').textContent = data.ringkasan.jumlah_keluar;
                el('totalMasuk').textContent = '+ ' + rupiah(data.ringkasan.masuk);
                el('totalKeluar').textContent = '− ' + rupiah(data.ringkasan.keluar);
                el('totalSelisih').textContent = (data.ringkasan.selisih < 0 ? '− ' : '') + rupiah(data.ringkasan.selisih);
            } catch (error) {
                console.error(error);
            }
        }

        document.querySelectorAll('input[name="jenis"]').forEach(radio =>
            radio.addEventListener('change', () => { HALAMAN = 1; muatTransaksi(); })
        );
        document.querySelector('select[name="periode"]').addEventListener('change', () => { HALAMAN = 1; muatTransaksi(); });
        document.querySelector('select[name="kategori"]').addEventListener('change', () => { HALAMAN = 1; muatTransaksi(); });

        formPengeluaran.addEventListener('submit', async event => {
            event.preventDefault();

            const tombol = el('btnSimpanPengeluaran');
            tombol.disabled = true;

            try {
                const response = await fetch(`${API_BASE}/pengeluaran`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: new FormData(formPengeluaran),
                });

                const hasil = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const pesan = hasil.errors
                        ? Object.values(hasil.errors).flat().join(' ')
                        : (hasil.message || 'Gagal menyimpan pengeluaran.');
                    throw new Error(pesan);
                }

                formPengeluaran.reset();
                location.hash = '';
                HALAMAN = 1;
                muatTransaksi();
                alert(hasil.message || 'Pengeluaran berhasil dicatat.');
            } catch (error) {
                alert(error.message);
            } finally {
                tombol.disabled = false;
            }
        });

        document.getElementById('btnExport').addEventListener('click', event => {
            event.preventDefault();
            const params = new URLSearchParams({
                jenis: jenisTerpilih(),
                periode: periodeTerpilih(),
            });
            if (kategoriTerpilih()) params.set('kategori', kategoriTerpilih());
            window.location.href = `${API_BASE}/transaksi/export?${params}`;
        });

        muatTransaksi();
    </script>
</body>
</html>