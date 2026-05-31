@props(['links'])

<div x-show="open" x-cloak class="border-t border-slate-100 py-3 md:hidden">
    <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
        <x-navbar.avatar />
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <div class="space-y-1">
        @foreach ($links as $link)
            <x-navbar.mobile-link :href="$link['href']" :active="$link['active']">{{ $link['label'] }}</x-navbar.mobile-link>
        @endforeach
    </div>

    <div class="mt-3 space-y-1 border-t border-slate-100 pt-3">
        <x-navbar.mobile-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profil Saya</x-navbar.mobile-link>
        <x-navbar.mobile-link :href="route('preferences.edit')" :active="request()->routeIs('preferences.*')">Atur Preferensi</x-navbar.mobile-link>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                Logout
            </button>
        </form>
    </div>
</div>
