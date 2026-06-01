<x-app-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <x-ui.page-header title="Atur Preferensi" description="Sesuaikan tampilan PitStop agar nyaman digunakan." />

        @if (session('success'))
            <x-ui.alert variant="success" class="mt-5">{{ session('success') }}</x-ui.alert>
        @endif

        <form action="{{ route('preferences.update') }}" method="POST" class="mt-6">
            @csrf
            @method('PATCH')

            <x-ui.card>
                <fieldset>
                    <legend class="text-base font-semibold text-blue-900">Tema Tampilan</legend>
                    <p class="mt-1 text-sm text-slate-500">Pilih tema PitStop.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach (['light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="theme" value="{{ $value }}" class="peer sr-only"
                                    @checked(old('theme', $theme) === $value)>
                                <span
                                    class="block rounded-xl border border-slate-200 p-4 transition peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <span class="block font-semibold text-slate-700">{{ $label }}</span>
                                    <span
                                        class="mt-1 block text-xs text-slate-500">{{ $value === 'light' ? 'Tampilan terang dengan warna yang bersih.' : 'Tampilan gelap yang nyaman di tempat redup.' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-form.error name="theme" />
                </fieldset>

                <fieldset class="mt-7 border-t border-slate-200 pt-6">
                    <legend class="text-base font-semibold text-blue-900">Ukuran Font</legend>
                    <p class="mt-1 text-sm text-slate-500">Atur ukuran teks sesuai kebutuhanmu.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach (['normal' => 'Normal', 'large' => 'Large'] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="font_size" value="{{ $value }}" class="peer sr-only"
                                    @checked(old('font_size', $fontSize) === $value)>
                                <span
                                    class="block rounded-xl border border-slate-200 p-4 transition peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                    <span class="block font-semibold text-slate-700">{{ $label }}</span>
                                    <span
                                        class="mt-1 block text-xs text-slate-500">{{ $value === 'normal' ? 'Ukuran teks standar.' : 'Teks lebih besar dan mudah dibaca.' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <x-form.error name="font_size" />
                </fieldset>

                <x-ui.button type="submit" class="mt-7 w-full sm:w-auto">Simpan Preferensi</x-ui.button>
            </x-ui.card>
        </form>
    </div>
</x-app-layout>
