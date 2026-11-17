<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Chikumi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F2] font-['Poppins'] text-[#1E1B18]">

    <main class="group mx-auto max-w-md space-y-4 px-5 pb-8 pt-4 sm:max-w-lg">

        <!-- APP BAR -->
        <header class="flex items-center gap-3 py-1.5">
            <a href="/customer/checkout" aria-label="Kembali"
                class="grid h-10 w-10 place-items-center rounded-xl border-[1.5px] border-[#EDE6DF] bg-white transition hover:border-[#FF8000]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h1 class="text-lg font-bold">Pembayaran</h1>
        </header>

        <!-- PILIH METODE: QRIS / VIRTUAL ACCOUNT -->
        <div class="flex gap-1 rounded-[14px] bg-[#F7F3EF] p-1">
            <label class="flex flex-1 cursor-pointer justify-center rounded-[10px] py-2.5 text-sm font-medium text-[#6B645D] transition has-checked:bg-white has-checked:font-semibold has-checked:text-[#D96C00]">
                <input type="radio" name="payment_method" value="qris" id="bayar-qris" class="sr-only" checked>QRIS
            </label>
            <label class="flex flex-1 cursor-pointer justify-center rounded-[10px] py-2.5 text-sm font-medium text-[#6B645D] transition has-checked:bg-white has-checked:font-semibold has-checked:text-[#D96C00]">
                <input type="radio" name="payment_method" value="va" id="bayar-va" class="sr-only">Virtual Account
            </label>
        </div>

        <!-- TOTAL + BATAS WAKTU (data contoh) -->
        <section class="flex flex-col items-center gap-1 rounded-[20px] bg-[#FF8000] p-[18px] text-white">
            <p class="text-xs text-[#FFF3E6]">Total pembayaran · <span id="nomorPesanan">#CK-0134</span></p>
            <p id="totalBayar" class="text-[30px] font-bold">Rp 69.000</p>
            <span class="flex items-center gap-1.5 rounded-full bg-[#D96C00] px-3 py-1 text-xs font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Bayar dalam <span id="sisaWaktu">09:41</span>
            </span>
        </section>

        <!-- PANEL QRIS -->
        <section class="group-has-[#bayar-va:checked]:hidden">
            <div class="flex flex-col items-center gap-2.5 rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px]">
                <!-- gambar QR contoh: backend ganti dengan QR dari payment gateway -->
                <div id="qrisGambar" class="relative h-[220px] w-[220px] rounded-lg bg-white">
                    <svg viewBox="0 0 220 220" class="h-full w-full fill-[#1E1B18]" aria-label="Kode QR pembayaran">
                        <rect x="8" y="8" width="52.8" height="52.8" rx="6" />
                        <rect x="15.9" y="15.9" width="37" height="37" rx="4" fill="#fff" />
                        <rect x="23.8" y="23.8" width="21.1" height="21.1" rx="3" />
                        <rect x="159.2" y="8" width="52.8" height="52.8" rx="6" />
                        <rect x="167.1" y="15.9" width="37" height="37" rx="4" fill="#fff" />
                        <rect x="175" y="23.8" width="21.1" height="21.1" rx="3" />
                        <rect x="8" y="159.2" width="52.8" height="52.8" rx="6" />
                        <rect x="15.9" y="167.1" width="37" height="37" rx="4" fill="#fff" />
                        <rect x="23.8" y="175" width="21.1" height="21.1" rx="3" />
                        <rect x="8" y="71.75" width="10.8" height="10.8" rx="1.5" /> <rect x="8" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="8" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="8" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="20.75" y="122.75" width="10.8" height="10.8" rx="1.5" />
                                <rect x="20.75" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="33.5" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="33.5" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="46.25" y="71.75" width="10.8" height="10.8" rx="1.5" /> <rect x="46.25" y="84.5" width="10.8" height="10.8" rx="1.5" />
                                <rect x="59" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="59" y="110" width="10.8" height="10.8" rx="1.5" /> <rect x="59" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="71.75" y="8" width="10.8" height="10.8" rx="1.5" /> <rect x="71.75" y="46.25" width="10.8" height="10.8" rx="1.5" />
                                <rect x="71.75" y="59" width="10.8" height="10.8" rx="1.5" /> <rect x="71.75" y="122.75" width="10.8" height="10.8" rx="1.5" /> <rect x="71.75" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="71.75" y="161" width="10.8" height="10.8" rx="1.5" /> <rect x="84.5" y="8" width="10.8" height="10.8" rx="1.5" />
                                <rect x="84.5" y="20.75" width="10.8" height="10.8" rx="1.5" /> <rect x="84.5" y="33.5" width="10.8" height="10.8" rx="1.5" /> <rect x="84.5" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="84.5" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="84.5" y="161" width="10.8" height="10.8" rx="1.5" />
                                <rect x="84.5" y="199.25" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="8" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="20.75" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="110" width="10.8" height="10.8" rx="1.5" />
                                <rect x="97.25" y="122.75" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="148.25" width="10.8" height="10.8" rx="1.5" /> <rect x="97.25" y="199.25" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="20.75" width="10.8" height="10.8" rx="1.5" />
                                <rect x="110" y="33.5" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="59" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="122.75" width="10.8" height="10.8" rx="1.5" />
                                <rect x="110" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="148.25" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="173.75" width="10.8" height="10.8" rx="1.5" /> <rect x="110" y="199.25" width="10.8" height="10.8" rx="1.5" /> <rect x="122.75" y="33.5" width="10.8" height="10.8" rx="1.5" />
                                <rect x="122.75" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="122.75" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="122.75" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="122.75" y="161" width="10.8" height="10.8" rx="1.5" /> <rect x="122.75" y="186.5" width="10.8" height="10.8" rx="1.5" />
                                <rect x="122.75" y="199.25" width="10.8" height="10.8" rx="1.5" /> <rect x="135.5" y="8" width="10.8" height="10.8" rx="1.5" /> <rect x="135.5" y="20.75" width="10.8" height="10.8" rx="1.5" /> <rect x="135.5" y="33.5" width="10.8" height="10.8" rx="1.5" /> <rect x="135.5" y="97.25" width="10.8" height="10.8" rx="1.5" />
                                <rect x="135.5" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="135.5" y="186.5" width="10.8" height="10.8" rx="1.5" /> <rect x="148.25" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="148.25" y="161" width="10.8" height="10.8" rx="1.5" /> <rect x="148.25" y="186.5" width="10.8" height="10.8" rx="1.5" />
                                <rect x="161" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="161" y="110" width="10.8" height="10.8" rx="1.5" /> <rect x="173.75" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="173.75" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="173.75" y="161" width="10.8" height="10.8" rx="1.5" />
                                <rect x="173.75" y="186.5" width="10.8" height="10.8" rx="1.5" /> <rect x="173.75" y="199.25" width="10.8" height="10.8" rx="1.5" /> <rect x="186.5" y="84.5" width="10.8" height="10.8" rx="1.5" /> <rect x="186.5" y="97.25" width="10.8" height="10.8" rx="1.5" /> <rect x="186.5" y="110" width="10.8" height="10.8" rx="1.5" />
                                <rect x="186.5" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="186.5" y="161" width="10.8" height="10.8" rx="1.5" /> <rect x="186.5" y="186.5" width="10.8" height="10.8" rx="1.5" /> <rect x="199.25" y="71.75" width="10.8" height="10.8" rx="1.5" /> <rect x="199.25" y="84.5" width="10.8" height="10.8" rx="1.5" />
                                <rect x="199.25" y="135.5" width="10.8" height="10.8" rx="1.5" /> <rect x="199.25" y="148.25" width="10.8" height="10.8" rx="1.5" /> <rect x="199.25" y="199.25" width="10.8" height="10.8" rx="1.5" />
                    </svg>
                    <span class="absolute left-[88px] top-[88px] grid h-11 w-11 place-items-center rounded-lg bg-white text-[22px] font-extrabold italic text-[#FF8000]">C</span>
                </div>
                <p class="text-xs text-[#6B645D]">Nominal sudah otomatis — tinggal scan &amp; bayar</p>
            </div>

            <div class="mt-4 flex gap-2.5">
                <button type="button" id="btnSimpanQr"
                    class="flex-1 rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
                    Simpan QR
                </button>
                <button type="button" id="btnBukaEwallet"
                    class="flex-1 rounded-[14px] border-[1.5px] border-[#EDE6DF] bg-white py-[15px] text-base font-semibold transition hover:border-[#FF8000]">
                    Buka E-Wallet
                </button>
            </div>
        </section>

        <!-- PANEL VIRTUAL ACCOUNT (data contoh) -->
        <section class="hidden rounded-[20px] border-[1.5px] border-[#EDE6DF] bg-white p-[18px] group-has-[#bayar-va:checked]:block">
            <p class="text-xs text-[#6B645D]">Bank</p>
            <p id="vaBank" class="mt-0.5 text-sm font-semibold">BCA Virtual Account</p>

            <p class="mt-4 text-xs text-[#6B645D]">Nomor Virtual Account</p>
            <div class="mt-1 flex items-center justify-between gap-3 rounded-xl bg-[#F7F3EF] px-4 py-3">
                <span id="vaNomor" class="text-lg font-bold tracking-wide">8808 0823 1234 5678</span>
                <button type="button" id="btnSalinVa" class="text-sm font-semibold text-[#FF8000] hover:underline">Salin</button>
            </div>

            <p class="mt-3 text-xs text-[#6B645D]">Transfer sesuai total lewat m-banking, ATM, atau internet banking.</p>
        </section>

        <!-- STATUS PEMBAYARAN -->
        <div id="statusBayar" class="flex items-center justify-center gap-2.5 rounded-full bg-[#FFF4D6] px-4 py-3">
            <span class="h-[18px] w-[18px] animate-spin rounded-full border-2 border-[#C27C00] border-t-transparent"></span>
            <p class="text-[13px] font-semibold text-[#C27C00]">Menunggu pembayaran…</p>
        </div>

        <p class="text-center text-xs text-[#6B645D]">
            Belum sempat bayar? Lanjutkan nanti dari menu Pesanan selama belum kedaluwarsa.
        </p>
    </main>

</body>

</html>