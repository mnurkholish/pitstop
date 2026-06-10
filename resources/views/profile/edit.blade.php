<x-app-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <x-ui.page-header title="Profil Saya" description="Kelola informasi akun dan keamanan profilmu." />

        <x-ui.card class="mt-6">
            <div class="flex flex-col gap-4 pb-5 sm:flex-row sm:items-center">
                <x-navbar.avatar class="size-16 text-lg" />
                <div>
                    <h2 class="text-lg font-bold text-blue-900">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-blue-600">
                        {{ $user->role === 'admin' ? 'Admin' : 'Pelanggan' }}</p>
                </div>
            </div>
        </x-ui.card>

        <div class="mt-6 space-y-6">
            {{-- Form profil dan keamanan --}}
            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
