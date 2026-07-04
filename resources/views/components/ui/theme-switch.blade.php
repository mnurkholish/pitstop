@php
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true)
        ? request()->cookie('pitstop_theme')
        : 'light';
    $nextThemeLabel = $theme === 'dark' ? 'terang' : 'gelap';
@endphp

<button
    type="button"
    data-theme-switch
    aria-label="Ganti ke tema {{ $nextThemeLabel }}"
    aria-pressed="{{ $theme === 'dark' ? 'true' : 'false' }}"
    title="Ganti ke tema {{ $nextThemeLabel }}"
    {{ $attributes->merge(['class' => 'inline-flex size-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2']) }}
>
    <svg data-theme-icon="moon" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.8 6.8 0 0 0 9.8 9.8Z" />
    </svg>
    <svg data-theme-icon="sun" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.06 16.94l-1.42 1.42m12.72 0-1.42-1.42M7.06 7.06 5.64 5.64M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z" />
    </svg>
</button>
