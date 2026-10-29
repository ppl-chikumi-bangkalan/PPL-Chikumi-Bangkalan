<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css'])

</head>

<body class="min-h-screen-bg-[#FFF9F5] flex items-center justify-center font-poppins">

    {{-- mockup HP --}}
    <div
        class="relative w-[390px] h-[844px] overflow-hidden rounded-[45px] border-[10px] border-[#222222] bg-[#FFF3e6] shadow-2xl">

        <div class="h-full overflow-y-auto">
            <section class="bg-[#FF8000] px-6 pt-14 pb-20 relative overflow-hidden">
                <div class="absolute -right-10 -top-12 h-64 w-64 rounded-full bg-[#FFA033] opacity-70"></div>
                <div class="absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-[#E87500] opacity-80"></div>
                <div class="relative z-10">
                    <h1 class="text-2xl font-extrabold italic text-white">Chikumi</h1>
                    <p class="text-white text-[7px] pb-4">JAPANESE SNACK</p>

                    <h1 class="text-white italic font-bold text-xl">
                        Jajan Chikumi <br>
                        jadi lebih mudah.
                    </h1>
                    <p class="text-white text-[9px]">Pesan Chikumi favoritmu!!</p>
                </div>

            </section>

            <section class="mt-10 px-5 pb-10">
                <div class="rounded-3xl p-6 bg-white ">
                    <h3 class="font-bold">Masuk ke Akun</h3>
                    <p class="text-[8px] text-neutral-400 justify-center">Gunakan email/no. WhatsApp dan passsword.
                        Sistem otomatis mengarahkan ke halaman sesuai hak aksesmu.</p>

                    {{-- LOGIN EMAIL ATAU WA --}}
                    <div class="mt-2">
                        {{-- EMAIL --}}
                        <label for="email" class="text-[8px] ">
                            Email atau No. WhtasApp
                        </label> <br>

                        <div class="relative">
                            {{-- icon person --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="absolute left-2 top-1/2 size-3 -translate-y-1/2 text-[#FF8000]">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>

                            <input
                                class="text-[7px] w-full rounded-lg border border-[#FF8000] px-3 py-2 outline-none py-2 pl-7 pr-3"
                                type="text" id="email" name="email"
                                placeholder="Masukkan Email atau No. WhtasApp Anda">

                        </div>

                    </div>


                    <div>
                        {{-- PASSWORD --}}
                        <label for="password" class="text-[8px]">
                            Password
                        </label>

                        <div class="relative">

                            {{-- IKON GEMBOK --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="absolute left-2 top-1/2 size-3 -translate-y-1/2 text-black">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>

                            {{-- INPUT PASSWORD --}}
                            <input type="password" name="password" id="password" placeholder="Masukkan Password"
                                class="w-full rounded-lg border border-[#FF8000] py-2 pl-7 pr-3 text-[7px] outline-none">

                        </div>
                    </div>


                    <div class="mt-3 flex items-center justify-between">
                        <label for="option"
                            class="text-[8px] flex items-center gap-2 accent-[#FF8000] text-neutral-500">
                            <input type="checkbox"> Ingat saya
                        </label>

                        <a href="#" class="text-[8px] text-[#FF8000]">Lupa password?</a>
                    </div>

                    {{-- Masuk akun --}}
                    <button type="button"
                        class="w-full rounded-l text-white bg-[#FF8000] p-1 mt-2 text-[8px] transition hover:bg-[#FFF3e6] hover:text-[#FF8000] font-semibold">Masuk</button>

                    <div class="my-2 flex items-center gap-2">

                        <div class="h-px flex-1 bg-neutral-200"></div>

                        <span class="text-[7px] text-neutral-400">Belum punya akun?</span>

                        <div class="h-px flex-1 bg-neutral-200"></div>

                    </div>

                    {{-- PELANGGAN BARU --}}
                    <a href="#"
                        class="block w-full rounded-l text-black bg-white border border-neutral-200 text-center p-1 text-[8px] font-semibold">Daftar
                        Sebagai Pelanggan</a>

                </div>


                {{-- FOOTER --}}
                <div class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-2 pb-5">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-3 shrink-0 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>

                    <p class="text-center text-[8px] text-gray-400">
                        Akun Kasir & Admin hanya dibuat oleh Owner melalui menu Pengguna
                    </p>

                </div>

            </section>
        </div>

    </div>
</body>

</html>
