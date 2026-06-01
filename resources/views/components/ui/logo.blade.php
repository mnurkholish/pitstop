@props(['admin' => false])

@php
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true)
        ? request()->cookie('pitstop_theme')
        : 'light';
    $lightLogo = 'images/logo-pitstop-light.png';
    $logo = $theme === 'dark' && file_exists(public_path('images/logo-pitstop-dark.png'))
        ? 'images/logo-pitstop-dark.png'
        : $lightLogo;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 font-bold tracking-tight text-blue-900']) }}>
    <img
        src="{{ asset($logo) }}"
        alt="{{ $admin ? 'PitStop Admin' : 'PitStop' }}"
        width="500"
        height="500"
        class="size-10 shrink-0 object-contain"
    >
    <span class="{{ $admin ? '' : 'sr-only' }}">PitStop{{ $admin ? ' Admin' : '' }}</span>
</span>
