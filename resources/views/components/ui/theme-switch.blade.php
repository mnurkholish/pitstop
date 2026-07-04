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
    {{ $attributes->merge(['class' => 'inline-flex h-12 w-9 shrink-0 select-none items-center justify-center bg-transparent p-0 text-slate-500 transition hover:scale-105 focus:outline-none focus-visible:drop-shadow-[0_0_0.35rem_rgba(59,130,246,0.7)]']) }}
>
    <svg class="h-12 w-9 overflow-visible" viewBox="0 0 72 118" fill="none" aria-hidden="true">
        <g data-theme-glow>
            <circle cx="36" cy="30" r="27" />
        </g>

        <g data-theme-bulb>
            <circle data-theme-bulb-glass cx="36" cy="30" r="25" />
            <ellipse data-theme-bulb-shine cx="47" cy="21" rx="7" ry="11" transform="rotate(-24 47 21)" />
            <path data-theme-neck d="M25 52h22v7c0 2.2-1.8 4-4 4H29c-2.2 0-4-1.8-4-4v-7Z" />
            <path data-theme-base d="M28 62h16v12c0 3.3-2.7 6-6 6h-4c-3.3 0-6-2.7-6-6V62Z" />
            <path data-theme-screw d="M27.5 66.5h17M28 71h16M30 75.5h12" />
        </g>

        <g data-theme-rope>
            <path data-theme-cord d="M36 80V112" />
            <g data-theme-pull>
                <circle cx="36" cy="108" r="5" />
            </g>
        </g>
    </svg>
</button>
