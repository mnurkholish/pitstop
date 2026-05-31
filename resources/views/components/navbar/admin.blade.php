@php
    $links = [
        ['label' => 'Dashboard Admin', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ['label' => 'Kelola Layanan', 'href' => url('/admin/services'), 'active' => request()->is('admin/services*')],
        ['label' => 'Daftar Booking', 'href' => url('/admin/bookings'), 'active' => request()->is('admin/bookings')],
        ['label' => 'Riwayat Booking', 'href' => url('/admin/bookings/history'), 'active' => request()->is('admin/bookings/history')],
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
