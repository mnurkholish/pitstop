<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-xl font-bold text-blue-900">Lupa Password</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">
            Masukkan email untuk menerima tautan reset password.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Kirim Tautan Reset Password
            </x-primary-button>
        </div>
    </form>

    <x-auth.navigation :back-href="route('login')" back-label="Kembali ke halaman masuk" prompt="Belum punya akun?"
        :action-href="route('register')" action-label="Daftar" />
</x-guest-layout>
