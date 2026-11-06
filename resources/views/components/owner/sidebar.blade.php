@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- SIDEBAR -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-[248px] shrink-0 -translate-x-full flex-col border-r border-[#EDE6DF] bg-white px-5 pb-8 pt-8 transition-transform duration-300 lg:static lg:translate-x-0">

    <div>
        <h1 class="text-[32px] font-extrabold italic leading-tight text-[#FF8000]">Chikumi</h1>
        <p class="text-[10px] font-semibold tracking-wide text-[#1E1B18]">JAPANESE SNACK</p>
    </div>

    <nav class="mt-10 flex-1 space-y-1.5">
        <p class="mb-2 text-[11px] font-medium text-[#A39B93]">
            MENU OWNER
        </p>

        <!-- Dashboard -->
        <a href="{{ route('owner.dashboard') }}"
            class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] transition
            {{ $currentRoute === 'owner.dashboard'
                ? 'bg-[#FF8000] font-semibold text-white'
                : 'text-[#6B645D] hover:bg-[#F7F3EF] hover:text-[#FF8000]' }}">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <rect x="3.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                <rect x="13.75" y="3.75" width="6.5" height="6.5" rx="1.5" />
                <rect x="3.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
                <rect x="13.75" y="13.75" width="6.5" height="6.5" rx="1.5" />
            </svg>

            <span>Dashboard</span>
        </a>

        <!-- Produk & Stok -->
        <a href="{{ route('owner.stok') }}"
            class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] transition
            {{ in_array($currentRoute, ['owner.kelola-produk', 'owner.stok', 'owner.produk', 'owner.produk.edit'])
                ? 'bg-[#FF8000] font-semibold text-white'
                : 'text-[#6B645D] hover:bg-[#F7F3EF] hover:text-[#FF8000]' }}">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>

            <span>Produk &amp; Stok</span>
        </a>

        <!-- Transaksi -->
        <a href="#"
            class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M5.25 3.75h13.5v16.5l-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5-2.25-1.5-2.25 1.5V3.75Z" />
                <path stroke-linecap="round" d="M9 8.25h6M9 11.25h6M9 14.25h3.75" />
            </svg>

            <span>Transaksi</span>
        </a>

        <!-- Laporan -->
        <a href="#"
            class="flex items-center gap-3 rounded-xl px-3.5 py-3 text-[15px] text-[#6B645D] transition hover:bg-[#F7F3EF] hover:text-[#FF8000]">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3.75v16.5h16.5M8.25 16.5v-4.5M12 16.5V9M15.75 16.5v-6M19.5 16.5V6.75" />
            </svg>

            <span>Laporan</span>
        </a>
    </nav>

    <!-- PROFILE OWNER -->
    <div class="flex items-center gap-3 rounded-xl bg-[#F7F3EF] p-3">
        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#FF8000] text-base font-bold text-white">
            O
        </div>

        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-[#1E1B18]">Owner Chikumi</p>
            <p class="text-xs text-[#6B645D]">Owner</p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" aria-label="Logout"
                class="rounded-lg p-1.5 text-[#E03131] transition hover:bg-white">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6"
                    stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>

            </button>
        </form>
    </div>
</aside>