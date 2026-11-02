@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- SIDEBAR -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 -translate-x-full flex-col border-r border-orange-100 bg-white transition-transform duration-300 lg:static lg:translate-x-0">

    <div class="border-b border-orange-100 px-6 py-5">
        <h1 class="text-2xl font-extrabold italic text-[#FF8000]">Chikumi</h1>
        <p class="text-[9px] font-bold tracking-wider">JAPANESE SNACK</p>
    </div>

    <nav class="flex-1 space-y-2 p-4 text-sm">
        <p class="mb-3 px-3 text-[10px] font-semibold text-stone-400">
            MENU ADMIN
        </p>

        <!-- Dashboard -->
        <a href="{{ route('owner.dashboard') }}"
            class="flex items-center gap-3 rounded-lg px-3 py-3 transition
    {{ $currentRoute === 'owner.dashboard'
        ? 'bg-[#FF8000] font-semibold text-white'
        : 'text-stone-600 hover:bg-orange-50 hover:text-[#FF8000]' }}">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <rect x="3" y="3" width="8" height="8" rx="2" />
                <rect x="13" y="3" width="8" height="5" rx="2" />
                <rect x="13" y="10" width="8" height="11" rx="2" />
                <rect x="3" y="13" width="8" height="8" rx="2" />
            </svg>

            <span>Dashboard</span>
        </a>

        <!-- Kelola Produk -->
        <a href="{{ route('owner.stok') }}"
            class="flex items-center gap-3 rounded-lg px-3 py-3 transition
            {{ in_array($currentRoute, ['owner.stok', 'owner.produk', 'owner.produk.edit'])
                ? 'bg-[#FF8000] font-semibold text-white'
                : 'text-stone-600 hover:bg-orange-50 hover:text-[#FF8000]' }}">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>

            <span>Kelola Produk</span>
        </a>

        <!-- Transaksi -->
        <a href="#"
            class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>

            <span>Transaksi</span>
        </a>

        <!-- Laporan -->
        <a href="#"
            class="flex items-center gap-3 rounded-lg px-3 py-3 text-stone-600 transition hover:bg-orange-50 hover:text-[#FF8000]">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
            </svg>

            <span>Laporan</span>
        </a>
    </nav>

    <!-- PROFILE OWNER -->
    <div class="m-3 flex items-center gap-3 rounded-xl bg-orange-50 p-3">
        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#FF8000] text-sm font-bold text-white">
            A
        </div>

        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold">Admin Chikumi</p>
            <p class="text-[10px] text-stone-500">Owner</p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" aria-label="Logout"
                class="rounded-lg p-2 text-red-500 transition hover:bg-orange-100">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>

            </button>
        </form>
    </div>
</aside>
