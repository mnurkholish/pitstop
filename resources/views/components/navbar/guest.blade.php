@php
    $links = [
        ['label' => 'Beranda', 'href' => url('/'), 'active' => request()->is('/')],
        ['label' => 'Layanan', 'href' => url('/services'), 'active' => request()->is('services*')],
        ['label' => 'Tentang', 'href' => url('/about'), 'active' => request()->is('about')],
        ['label' => 'Kontak', 'href' => url('/contact'), 'active' => request()->is('contact')],
    ];
@endphp

<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white" aria-label="Navigasi publik">
    <div class="pitstop-container">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ url('/') }}" aria-label="PitStop Beranda">
                <x-ui.logo />
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @foreach ($links as $link)
                    <x-navbar.link :href="$link['href']" :active="$link['active']">{{ $link['label'] }}</x-navbar.link>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 md:flex">
                <x-ui.button href="{{ route('login') }}" variant="secondary" size="sm">Masuk</x-ui.button>
                <x-ui.button href="{{ route('register') }}" size="sm">Daftar</x-ui.button>
            </div>

            <x-navbar.hamburger />
        </div>

        <div x-show="open" x-cloak class="space-y-1 border-t border-slate-100 py-3 md:hidden">
            @foreach ($links as $link)
                <x-navbar.mobile-link :href="$link['href']" :active="$link['active']">{{ $link['label'] }}</x-navbar.mobile-link>
            @endforeach
            <div class="grid grid-cols-2 gap-2 pt-3">
                <x-ui.button href="{{ route('login') }}" variant="secondary" size="sm">Masuk</x-ui.button>
                <x-ui.button href="{{ route('register') }}" size="sm">Daftar</x-ui.button>
            </div>
        </div>
    </div>
</nav>
