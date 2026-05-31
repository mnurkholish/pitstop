@php
    $links = [
        ['label' => 'Dashboard Admin', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Kelola Layanan', 'href' => route('admin.services.index'), 'active' => request()->routeIs('admin.services.*')],
        ['label' => 'Daftar Booking', 'href' => route('admin.bookings.index'), 'active' => request()->routeIs('admin.bookings.index')],
        ['label' => 'Riwayat Booking', 'href' => route('admin.bookings.history'), 'active' => request()->routeIs('admin.bookings.history')],
    ];
@endphp

<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white" aria-label="Navigasi admin">
    <div class="pitstop-container">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" aria-label="PitStop Dashboard Admin">
                <x-ui.logo admin />
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @foreach ($links as $link)
                    <x-navbar.link :href="$link['href']" :active="$link['active']">{{ $link['label'] }}</x-navbar.link>
                @endforeach
            </div>

            <div class="hidden md:block">
                <x-navbar.avatar-dropdown />
            </div>

            <x-navbar.hamburger />
        </div>

        <x-navbar.mobile-panel :links="$links" />
    </div>
</nav>
