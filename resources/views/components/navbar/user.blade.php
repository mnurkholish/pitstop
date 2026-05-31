@php
    $links = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Layanan', 'href' => url('/services'), 'active' => request()->is('services*')],
        ['label' => 'Booking Saya', 'href' => url('/my-bookings'), 'active' => request()->is('my-bookings*')],
    ];
@endphp

<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white" aria-label="Navigasi pelanggan">
    <div class="pitstop-container">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('dashboard') }}" aria-label="PitStop Dashboard">
                <x-ui.logo />
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
