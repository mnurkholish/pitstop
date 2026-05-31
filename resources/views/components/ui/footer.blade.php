<footer class="border-t border-slate-200 bg-white">
    <div class="pitstop-container flex flex-col gap-5 py-6 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <x-ui.logo class="text-sm" />
        <p>&copy; {{ now()->year }} PitStop. Semua hak dilindungi.</p>
        <div class="flex flex-wrap gap-x-4 gap-y-2">
            <a href="#" class="transition hover:text-blue-700">Kebijakan Privasi</a>
            <a href="#" class="transition hover:text-blue-700">Syarat &amp; Ketentuan</a>
        </div>
    </div>
</footer>
