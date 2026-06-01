@php
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true)
        ? request()->cookie('pitstop_theme')
        : 'light';
    $lightFavicon = 'favicons/pitstop-light.ico';
    $favicon = $theme === 'dark' && file_exists(public_path('favicons/pitstop-dark.ico'))
        ? 'favicons/pitstop-dark.ico'
        : $lightFavicon;
@endphp

<link rel="icon" href="{{ asset($favicon) }}" type="image/x-icon">
