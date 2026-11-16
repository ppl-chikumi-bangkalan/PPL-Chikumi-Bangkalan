<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <!-- TOPBAR KASIR -->
    <header class="sticky top-0 z-30 flex h-[76px] items-center gap-6 border-b border-[#EDE6DF] bg-white px-7">
        <div class="shrink-0">
            <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
            <p class="text-[10px] font-semibold tracking-wide">JAPANESE SNACK</p>
        </div>
        <nav class="flex items-center gap-1 rounded-xl bg-[#F7F3EF] p-1">
            <a href="/kasir/dashboard" class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm text-[#6B645D] transition hover:text-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                </svg>
                POS
            </a>
            <a href="/kasir/riwayat" class="flex items-center gap-2 rounded-lg bg-[#FF8000] px-4 py-2.5 text-sm font-semibold text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Riwayat
            </a>
        </nav>
        <div class="ml-auto flex items-center gap-4">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">K</span>
            <div class="leading-tight">
                <p class="text-sm font-semibold">Kasir</p>
                <p class="text-xs text-[#6B645D]">Kasir</p>
            </div>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" aria-label="Keluar" class="grid h-11 w-11 place-items-center rounded-xl border border-[#EDE6DF] bg-white text-[#E03131] transition hover:bg-[#FDECEC]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <main class="flex min-h-[calc(100vh-76px)]">

        <!-- DAFTAR TRANSAKSI -->
        <section class="group flex-1 p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold">Riwayat Transaksi</h2>
                    <p class="mt-0.5 text-sm text-[#6B645D]">Sabtu, 31 Okt 2026 · Shift 15.00–21.00</p>
                </div>
                <label class="mt-2 flex items-center gap-2 rounded-xl border border-[#EDE6DF] bg-white px-4 py-2.5 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-[#6B645D]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <select name="periode" class="cursor-pointer bg-transparent outline-none">
                        <option value="hari-ini">Hari ini</option>
                        <option value="kemarin">Kemarin</option>
                        <option value="7-hari">7 hari terakhir</option>
                    </select>
                </label>
            </div>

            <!-- FILTER SUMBER + CARI -->
            <div class="mt-5 flex items-center gap-2.5">
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-semua" value="semua" class="sr-only" checked>Semua
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-kasir" value="kasir" class="sr-only">Kasir
                </label>
                <label class="cursor-pointer rounded-full border border-[#EDE6DF] bg-white px-5 py-2.5 text-sm transition has-checked:border-[#FF8000] has-checked:bg-[#FF8000] has-checked:font-semibold has-checked:text-white">
                    <input type="radio" name="sumber" id="sumber-online" value="online" class="sr-only">Online
                </label>
                <label class="ml-auto flex w-[250px] items-center gap-2 rounded-xl border border-[#EDE6DF] bg-white px-4 py-2.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px] text-[#A39B93]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="search" name="cari" placeholder="Cari no. pesanan…" class="w-full bg-transparent text-sm outline-none placeholder:text-[#A39B93]">
                </label>
            </div>

            <!-- RINGKASAN -->
            <div class="mt-5 grid grid-cols-4 gap-3.5">
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">Transaksi</p>
                    <p class="mt-1 text-lg font-bold">24</p>
                </div>
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">Tunai</p>
                    <p class="mt-1 text-lg font-bold">Rp 356.000</p>
                </div>
                <div class="rounded-2xl border border-[#EDE6DF] bg-white p-4">
                    <p class="text-xs text-[#6B645D]">QRIS / VA</p>
                    <p class="mt-1 text-lg font-bold">Rp 256.000</p>
                </div>
                <div class="rounded-2xl bg-[#FF8000] p-4 text-white">
                    <p class="text-xs text-white/85">Total</p>
                    <p class="mt-1 text-lg font-bold">Rp 612.000</p>
                </div>
            </div>

            <!-- TABEL TRANSAKSI (data contoh) -->
            <div class="mt-5 overflow-hidden rounded-2xl border border-[#EDE6DF] bg-white">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F7F3EF] text-[11px] font-semibold tracking-wide text-[#6B645D]">
                        <tr>
                            <th class="py-2.5 pl-[18px] font-semibold">NO. PESANAN</th>
                            <th class="py-2.5 font-semibold">WAKTU</th>
                            <th class="py-2.5 font-semibold">SUMBER</th>
                            <th class="py-2.5 font-semibold">ITEM</th>
                            <th class="py-2.5 font-semibold">METODE</th>
                            <th class="py-2.5 font-semibold">TOTAL</th>
                            <th class="py-2.5 pr-[18px] font-semibold">STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="daftarTransaksi" class="divide-y divide-[#EDE6DF]">
                        <tr class="h-[50px] group-has-[#sumber-online:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0130</td>
                            <td class="text-xs text-[#6B645D]">16.58</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span></td>
                            <td>2× Reguler · Mentaiko · BBQ</td>
                            <td>Tunai</td>
                            <td class="font-semibold">Rp 40.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas</span></td>
                        </tr>
                        <tr class="h-[50px] group-has-[#sumber-online:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0127</td>
                            <td class="text-xs text-[#6B645D]">16.10</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span></td>
                            <td>1× Reguler · Tartar · Teriyaki</td>
                            <td>QRIS</td>
                            <td class="font-semibold">Rp 21.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas</span></td>
                        </tr>
                        <tr class="h-[50px] group-has-[#sumber-kasir:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0126</td>
                            <td class="text-xs text-[#6B645D]">15.45</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Online</span></td>
                            <td>1× Reguler · Mentaiko · Sweetcorn</td>
                            <td>VA</td>
                            <td class="font-semibold">Rp 20.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Selesai</span></td>
                        </tr>
                        <tr class="h-[50px] group-has-[#sumber-online:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0125</td>
                            <td class="text-xs text-[#6B645D]">15.32</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span></td>
                            <td>3 pcs Chikumi Reguler</td>
                            <td>QRIS</td>
                            <td class="font-semibold">Rp 61.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas</span></td>
                        </tr>
                        <!-- baris terpilih (detailnya tampil di panel kanan) -->
                        <tr class="h-[50px] bg-[#FFF9F2] shadow-[inset_3px_0_0_#FF8000] group-has-[#sumber-kasir:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0124</td>
                            <td class="text-xs text-[#6B645D]">15.20</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Online</span></td>
                            <td>2× Large · Garlic CC · Korean Spicy</td>
                            <td>QRIS</td>
                            <td class="font-semibold">Rp 54.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Selesai</span></td>
                        </tr>
                        <tr class="h-[50px] group-has-[#sumber-online:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0123</td>
                            <td class="text-xs text-[#6B645D]">15.08</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span></td>
                            <td>1× Reguler · Cream Cheese · BBQ</td>
                            <td>Tunai</td>
                            <td class="font-semibold">Rp 20.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas</span></td>
                        </tr>
                        <tr class="h-[50px] group-has-[#sumber-online:checked]:hidden">
                            <td class="pl-[18px] font-semibold">#CK-0122</td>
                            <td class="text-xs text-[#6B645D]">15.02</td>
                            <td><span class="inline-flex items-center gap-1.5 rounded-full bg-[#F7F3EF] px-2.5 py-1 text-xs font-medium text-[#6B645D]"><span class="h-1.5 w-1.5 rounded-full bg-[#A39B93]"></span>Kasir</span></td>
                            <td>3 pcs (Reguler &amp; Large)</td>
                            <td>Tunai</td>
                            <td class="font-semibold">Rp 74.000</td>
                            <td class="pr-[18px]"><span class="inline-flex items-center gap-1.5 rounded-full bg-[#E6F6EC] px-2.5 py-1 text-xs font-medium text-[#1F9D55]"><span class="h-1.5 w-1.5 rounded-full bg-[#1F9D55]"></span>Lunas</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- DETAIL TRANSAKSI -->
        <aside id="detailTransaksi" class="flex w-[380px] shrink-0 flex-col border-l border-[#EDE6DF] bg-white px-[25px] py-6">
            <div class="flex items-center justify-between">
                <h3 class="text-[22px] font-bold">#CK-0124</h3>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#FFF3E6] px-2.5 py-1 text-xs font-medium text-[#D96C00]"><span class="h-1.5 w-1.5 rounded-full bg-[#FF8000]"></span>Online</span>
            </div>
            <p class="mt-3 text-sm text-[#6B645D]">Sabtu, 31 Okt 2026 · 15.20 · Budi</p>

            <div class="mt-3.5 rounded-xl bg-[#F7F3EF] p-3.5">
                <div class="flex justify-between text-sm font-semibold">
                    <span>2× Large · Dip Sauce</span>
                    <span>Rp 54.000</span>
                </div>
                <p class="mt-1.5 text-xs text-[#6B645D]">Garlic Cream Cheese · Korean Spicy</p>
            </div>

            <div class="my-3.5 h-px bg-[#EDE6DF]"></div>

            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between">
                    <dt class="text-[#6B645D]">Metode</dt>
                    <dd class="font-semibold">QRIS</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B645D]">Pembayaran</dt>
                    <dd class="font-semibold text-[#1F9D55]">Lunas</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B645D]">Pengambilan</dt>
                    <dd class="font-semibold">Ambil di Tempat</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-[#6B645D]">Status</dt>
                    <dd class="font-semibold">Selesai · 15.40</dd>
                </div>
            </dl>

            <div class="mt-3.5 flex items-center justify-between rounded-xl bg-[#FFF3E6] px-3.5 py-3.5">
                <span class="text-base font-semibold">Total</span>
                <span class="text-[22px] font-bold text-[#FF8000]">Rp 54.000</span>
            </div>

            <!-- tombol cetak ulang: backend arahkan ke struk transaksi ini -->
            <button type="button" id="btnCetakUlang" class="mt-auto flex h-[52px] items-center justify-center gap-2 rounded-xl border-[1.5px] border-[#FF8000] bg-white text-base font-semibold text-[#FF8000] transition hover:bg-[#FFF3E6]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-[18px] w-[18px]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                </svg>
                Cetak Ulang Struk
            </button>
        </aside>
    </main>

</body>

</html>