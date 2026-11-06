@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- BOTTOM NAV PELANGGAN -->
<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EDE6DF] bg-white lg:hidden">
    <div class="mx-auto grid h-[82px] max-w-md grid-cols-3 px-4 sm:max-w-xl md:max-w-3xl">

        <!-- Beranda -->
        <a href="{{ route('customer.dashboard') }}"
            class="flex flex-col items-center justify-center gap-1 text-xs
            {{ $currentRoute === 'customer.dashboard' ? 'font-semibold text-[#FF8000]' : 'text-[#A39B93]' }}">

            <span class="grid h-[30px] w-[54px] place-items-center rounded-full
                {{ $currentRoute === 'customer.dashboard' ? 'bg-[#FFF3E6]' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-[22px] w-[22px]">
                    <rect x="3.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                    <rect x="13.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                    <rect x="3.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
                    <rect x="13.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
                </svg>
            </span>

            <span>Beranda</span>
        </a>

        <!-- Pesanan -->
        <a href="{{ Route::has('customer.pesanan') ? route('customer.pesanan') : '#' }}"
            class="flex flex-col items-center justify-center gap-1 text-xs
            {{ $currentRoute === 'customer.pesanan' ? 'font-semibold text-[#FF8000]' : 'text-[#A39B93]' }}">

            <span class="grid h-[30px] w-[54px] place-items-center rounded-full
                {{ $currentRoute === 'customer.pesanan' ? 'bg-[#FFF3E6]' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-[22px] w-[22px]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                    <path stroke-linecap="round" d="M9 8.25h6M9 11.25h6M9 14.25h3.75" />
                </svg>
            </span>

            <span>Pesanan</span>
        </a>

        <!-- Keranjang -->
        <a href="{{ Route::has('customer.keranjang') ? route('customer.keranjang') : '#' }}"
            class="flex flex-col items-center justify-center gap-1 text-xs
            {{ $currentRoute === 'customer.keranjang' ? 'font-semibold text-[#FF8000]' : 'text-[#A39B93]' }}">

            <span class="grid h-[30px] w-[54px] place-items-center rounded-full
                {{ $currentRoute === 'customer.keranjang' ? 'bg-[#FFF3E6]' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-[22px] w-[22px]">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
            </span>

            <span>Keranjang</span>
        </a>

    </div>
</nav>