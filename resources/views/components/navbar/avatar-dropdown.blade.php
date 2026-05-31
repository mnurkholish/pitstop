<x-dropdown width="56" contentClasses="overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
    <x-slot name="trigger">
        <button type="button" class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-left transition hover:bg-slate-50">
            <x-navbar.avatar />
            <span class="hidden lg:block">
                <span class="block text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                <span class="block text-xs text-slate-500">{{ Auth::user()->role === 'admin' ? 'Admin' : 'Pelanggan' }}</span>
            </span>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="border-b border-slate-100 px-4 py-3">
            <p class="text-sm font-semibold text-slate-700">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</p>
        </div>
        <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
        <x-dropdown-link href="{{ url('/preferences') }}">Atur Preferensi</x-dropdown-link>
        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
            @csrf
            <x-dropdown-link :href="route('logout')" class="text-red-600 hover:bg-red-50" onclick="event.preventDefault(); this.closest('form').submit();">
                Logout
            </x-dropdown-link>
        </form>
    </x-slot>
</x-dropdown>
