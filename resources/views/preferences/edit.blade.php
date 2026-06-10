<x-app-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <x-ui.page-header title="Atur Preferensi" description="Sesuaikan tampilan PitStop agar nyaman digunakan." />

        <div id="preference-success-message" hidden>
            <x-ui.alert variant="success" class="mt-5">
                <span data-preference-message>Preferensi tampilan berhasil disimpan.</span>
            </x-ui.alert>
        </div>

        <form id="preference-form" action="{{ route('preferences.update') }}" method="POST" class="mt-6">
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

                <div class="mt-7 flex flex-col gap-2 sm:flex-row">
                    <x-ui.button type="submit" class="w-full sm:w-auto">Simpan Preferensi</x-ui.button>
                    <x-ui.button type="button" id="preference-reset-button" variant="secondary" class="w-full sm:w-auto">Reset Preferensi</x-ui.button>
                </div>
            </x-ui.card>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const form = document.getElementById('preference-form');
                const resetButton = document.getElementById('preference-reset-button');
                const successMessage = document.getElementById('preference-success-message');
                const defaults = {
                    theme: 'light',
                    fontSize: 'normal',
                };

                if (! form) {
                    return;
                }

                const getCookie = (name) => {
                    return document.cookie
                        .split('; ')
                        .find((cookie) => cookie.startsWith(`${name}=`))
                        ?.split('=')
                        .slice(1)
                        .join('=') || null;
                };

                const setCookie = (name, value) => {
                    const maxAge = 60 * 60 * 24 * 365;
                    document.cookie = `${name}=${encodeURIComponent(value)}; max-age=${maxAge}; path=/; samesite=lax`;
                };

                const deleteCookie = (name) => {
                    document.cookie = `${name}=; max-age=0; path=/; samesite=lax`;
                };

                const replaceClassPrefix = (element, prefix, value) => {
                    Array.from(element.classList).forEach((className) => {
                        if (className.startsWith(prefix)) {
                            element.classList.remove(className);
                        }
                    });

                    element.classList.add(`${prefix}${value}`);
                };

                const getClassPreference = (prefix, fallback) => {
                    const className = Array.from(document.documentElement.classList)
                        .find((className) => className.startsWith(prefix));

                    return className ? className.slice(prefix.length) : fallback;
                };

                const checkInput = (name, value) => {
                    const input = form.querySelector(`[name="${name}"][value="${value}"]`);

                    if (input) {
                        input.checked = true;
                    }
                };

                const showSuccess = (message) => {
                    if (successMessage) {
                        const messageElement = successMessage.querySelector('[data-preference-message]');

                        if (messageElement) {
                            messageElement.textContent = message;
                        }

                        successMessage.hidden = false;
                    }
                };

                const applyPreferences = (theme, fontSize) => {
                    replaceClassPrefix(document.documentElement, 'pitstop-theme-', theme);
                    replaceClassPrefix(document.documentElement, 'pitstop-font-', fontSize);
                    checkInput('theme', theme);
                    checkInput('font_size', fontSize);
                };

                const storedTheme = decodeURIComponent(getCookie('pitstop_theme') || '');
                const storedFontSize = decodeURIComponent(getCookie('pitstop_font_size') || '');
                const currentTheme = getClassPreference('pitstop-theme-', defaults.theme);
                const currentFontSize = getClassPreference('pitstop-font-', defaults.fontSize);
                const theme = ['light', 'dark'].includes(storedTheme) ? storedTheme : currentTheme;
                const fontSize = ['normal', 'large'].includes(storedFontSize) ? storedFontSize : currentFontSize;

                applyPreferences(theme, fontSize);

                form.addEventListener('change', () => {
                    const formData = new FormData(form);
                    const theme = formData.get('theme');
                    const fontSize = formData.get('font_size');

                    if (theme && fontSize) {
                        applyPreferences(theme, fontSize);
                    }
                });

                resetButton?.addEventListener('click', () => {
                    deleteCookie('pitstop_theme');
                    deleteCookie('pitstop_font_size');
                    applyPreferences(defaults.theme, defaults.fontSize);
                    showSuccess('Preferensi tampilan berhasil direset.');
                });
            });
        </script>
    @endpush
</x-app-layout>
