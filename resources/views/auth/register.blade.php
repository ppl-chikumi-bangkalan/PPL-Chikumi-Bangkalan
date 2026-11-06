<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#FFF9F5] font-poppins">
    <div class="grid min-h-screen w-full grid-cols-1 lg:grid-cols-2">

        <!-- BAGIAN KIRI / BRANDING -->
        <section class="relative flex min-h-[240px] flex-col justify-center overflow-hidden bg-[#FF8000] px-6 py-10 sm:px-10 sm:py-12 lg:min-h-full lg:px-14">

            <!-- Dekorasi -->
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#FFA033] opacity-70"></div>
            <div class="absolute -bottom-20 -left-16 h-64 w-64 rounded-full bg-[#E87500] opacity-80"></div>
            <div class="absolute bottom-20 right-10 h-20 w-20 rounded-full bg-white opacity-10"></div>
            <img src="{{ asset('images/chikumi-1.png') }}" alt="Ilustrasi Chikumi"
                class="absolute right-0 top-0 z-0 h [150px] w-[164px] object-contain object-top">

            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold italic text-white sm:text-4xl lg:text-5xl">
                    Chikumi
                </h1>

                <p class="text-xs font-semibold text-white">
                    JAPANESE SNACK
                </p>

                <div class="mt-8 lg:mt-12">
                    <h2 class="text-2xl font-bold italic leading-snug text-white sm:text-3xl lg:text-4xl">
                        Jajan Chikumi
                        <br>
                        jadi lebih mudah.
                    </h2>

                    <p class="text-xm text-white">
                        Pesan Chikumi favoritmu!!
                    </p>
                </div>
            </div>
        </section>

        <!-- BAGIAN KANAN / FORM REGISTER -->
        <section class="flex flex-col items-center justify-center bg-[#FFF3E6] px-5 py-8 sm:px-10 sm:py-12 lg:px-14">

            <!-- CARD REGISTER -->
            <div class="bg-white p-5 rounded-2xl w-full max-w-2xl">

                <!-- KEMBALI KE LOGIN -->
                <a href="/login"
                    class="mb-7 inline-flex items-center gap-2 text-sm font-medium text-[#756D66] transition hover:text-[#FF8000]">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                    </svg>

                    Kembali ke Masuk
                </a>

                <!-- JUDUL -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#24211F] sm:text-3xl">
                        Buat Akun Pelanggan
                    </h1>

                    <p class="mt-2 text-sm leading-relaxed text-[#81766E]">
                        Isi data di bawah. Nomor WhatsApp dipakai untuk konfirmasi pesanan.
                    </p>
                </div>

                <!-- FORM REGISTER -->
                <form method="POST" action="#" class="space-y-5">
                    @csrf

                    <!-- NAMA LENGKAP -->
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-[#302C29]">
                            Nama Lengkap
                        </label>

                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.7" stroke="currentColor"
                                class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[#37312D]">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>

                            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap"
                                autocomplete="name" required value="{{ old('name') }}"
                                class="w-full rounded-xl border border-[#EDE3DC] bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-[#A49A92] focus:border-[#FF8000]">
                        </div>
                    </div>

                    <!-- WHATSAPP DAN EMAIL -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <!-- WHATSAPP -->
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-medium text-[#302C29]">
                                No. WhatsApp
                            </label>

                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.7" stroke="currentColor"
                                    class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[#37312D]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 3.75h3A2.25 2.25 0 0 1 15.75 6v12a2.25 2.25 0 0 1-2.25 2.25h-3A2.25 2.25 0 0 1 8.25 18V6a2.25 2.25 0 0 1 2.25-2.25ZM8.25 16.5h7.5" />
                                </svg>

                                <input type="tel" id="phone" name="phone" placeholder="08xx xxxx xxxx"
                                    autocomplete="tel" required value="{{ old('phone') }}"
                                    class="w-full rounded-xl border border-[#EDE3DC] bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-[#A49A92] focus:border-[#FF8000]">
                            </div>
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-[#302C29]">
                                Email
                            </label>

                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.7" stroke="currentColor"
                                    class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[#A49A92]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0-9.75 6.75L2.25 6.75" />
                                </svg>

                                <input type="email" id="email" name="email" placeholder="Masukkan email"
                                    autocomplete="email" required value="{{ old('email') }}"
                                    class="w-full rounded-xl border border-[#EDE3DC] bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-[#A49A92] focus:border-[#FF8000]">
                            </div>
                        </div>
                    </div>

                    <!-- PASSWORD DAN KONFIRMASI -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <!-- PASSWORD -->
                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-[#302C29]">
                                Password
                            </label>

                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.7" stroke="currentColor"
                                    class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[#A49A92]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>

                                <input type="password" id="password" name="password"
                                    placeholder="Masukkan password" autocomplete="new-password" required
                                    class="w-full rounded-xl border border-[#EDE3DC] bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-[#A49A92] focus:border-[#FF8000]">
                            </div>
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-[#302C29]">
                                Konfirmasi Password
                            </label>

                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.7" stroke="currentColor"
                                    class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[#A49A92]">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>

                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Ulangi password" autocomplete="new-password" required
                                    class="w-full rounded-xl border border-[#EDE3DC] bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition placeholder:text-[#A49A92] focus:border-[#FF8000]">
                            </div>

                            <p id="password-error" class="mt-2 hidden text-xs text-red-500">
                                Password tidak sama.
                            </p>
                        </div>
                    </div>

                    <!-- PERSETUJUAN -->
                    <div class="pt-1">
                        <label class="flex cursor-pointer items-center gap-2.5 text-sm text-[#81766E]">
                            <input type="checkbox" name="terms" value="1" required
                                class="size-5 rounded accent-[#FF8000]">

                            Saya menyetujui Syarat & Ketentuan Chikumi
                        </label>
                    </div>

                    <!-- TOMBOL DAFTAR -->
                    <button type="submit"
                        class="w-full rounded-xl bg-[#FF8000] px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-[#E87500]">
                        Daftar Sekarang
                    </button>

                </form>

                <!-- SUDAH PUNYA AKUN -->
                <div class="mt-5 text-center text-sm text-[#81766E]">
                    Sudah punya akun?

                    <a href="/login" class="ml-1 font-semibold text-[#D96B00] hover:underline">
                        Masuk
                    </a>
                </div>

            </div>

        </section>

    </div>

</body>

</html>
