<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-xl font-bold text-blue-900">Verifikasi Email</h1>
        <p class="mt-1 text-sm leading-6 text-slate-500">
            Periksa emailmu dan klik tautan verifikasi. Jika belum menerima email, tekan kirim ulang verifikasi.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Tautan verifikasi baru telah dikirim ke email yang kamu daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="rounded-md text-sm text-slate-600 underline transition hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Logout
            </button>
        </form>
    </div>

    <x-auth.navigation :back-href="route('home')" />
</x-guest-layout>
