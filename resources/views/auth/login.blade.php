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


        <!-- BAGIAN KANAN / FORM LOGIN -->
        <section class="bg-[#FFF3E6] flex flex-col items-center justify-center px-5 py-8 sm:px-10 sm:py-12 lg:px-14">

            <div class="bg-white p-5 rounded-2xl w-full max-w-md">

                <div class="mb-7">
                    <h2 class="text-2xl font-bold text-neutral-800 sm:text-3xl">
                        Masuk ke Akun
                    </h2>

                    <p class="mt-2 text-xs leading-relaxed text-neutral-500">
                        Gunakan email atau nomor WhatsApp dan password.
                        Sistem akan mengarahkanmu sesuai hak akses akun.
                    </p>
                </div>

                <!-- FORM LOGIN -->
                <form method="POST" class="space-y-5">
                    {{-- @csrf --}}

                    <!-- EMAIL / WHATSAPP -->
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-neutral-700">
                            Email atau No. WhatsApp
                        </label>

                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="absolute left-3 top-1/2 size-5 -translate-y-1/2 text-[#FF8000]">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>

                            <input type="text" id="email" name="email"
                                placeholder="Masukkan email atau nomor WhatsApp" autocomplete="username" required
                                class="w-full rounded-xl border border-neutral-300 bg-white py-3 pl-11 pr-4 text-sm outline-none transition focus:border-[#FF8000]">
                        </div>
                    </div>


                    <!-- PASSWORD -->
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-neutral-700">
                            Password
                        </label>

                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="absolute left-3 top-1/2 size-5 -translate-y-1/2 text-[#FF8000]">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>

                            <input type="password" id="password" name="password" placeholder="Masukkan password"
                                autocomplete="current-password" required
                                class="w-full rounded-xl border border-neutral-300 bg-white py-3 pl-11 pr-4 text-sm outline-none transition focus:border-[#FF8000]">
                        </div>
                    </div>


                    <!-- INGAT SAYA DAN LUPA PASSWORD -->
                    <div class="flex flex-wrap items-center justify-between gap-3 text-sm">

                        <label class="flex cursor-pointer items-center gap-2 text-neutral-600">
                            <input type="checkbox" name="remember" value="1" class="size-4 accent-[#FF8000]">
                            Ingat saya
                        </label>

                        <a href="#" class="font-medium text-[#FF8000] transition hover:text-[#E87500]">
                            Lupa password?
                        </a>
                    </div>


                    <!-- TOMBOL LOGIN -->
                    <button type="submit"
                        class="w-full rounded-xl bg-[#FF8000] px-5 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-[#FFF3E6] hover:text-[#FF8000]">
                        Masuk
                    </button>

                </form>


                <!-- DAFTAR -->
                <div class="my-6 flex items-center gap-3">
                    <div class="h-px flex-1 bg-neutral-200"></div>
                    <span class="text-xs text-neutral-400">
                        Belum punya akun?
                    </span>
                    <div class="h-px flex-1 bg-neutral-200"></div>
                </div>

                <a href="/register"
                    class="block w-full rounded-xl border border-neutral-200 bg-white px-5 py-3 text-center text-sm font-semibold text-neutral-700">
                    Daftar Sebagai Pelanggan
                </a>

            </div>
            <!-- FOOTER -->
            <p class="mt-10 w-full max-w-md text-center text-xs leading-relaxed text-neutral-400">
                Akun Kasir & Admin hanya dibuat oleh Owner
                melalui menu Pengguna.
            </p>
        </section>

    </div>

</body>

</html>
