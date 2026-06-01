<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-xl font-bold text-blue-900">Konfirmasi Password</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">
            Area ini dilindungi. Masukkan password untuk melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Password" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Konfirmasi
            </x-primary-button>
        </div>
    </form>

    <x-auth.navigation :back-href="route('home')" />
</x-guest-layout>
