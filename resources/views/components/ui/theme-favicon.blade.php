@php
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true)
        ? request()->cookie('pitstop_theme')
        : 'light';
    $lightFavicon = 'favicons/pitstop-light.ico';
    $darkFavicon = 'favicons/pitstop-dark.ico';
    $favicon = $theme === 'dark' && file_exists(public_path('favicons/pitstop-dark.ico'))
        ? $darkFavicon
        : $lightFavicon;
@endphp

<link
    rel="icon"
    href="{{ asset($favicon) }}"
    data-theme-favicon
    data-light-href="{{ asset($lightFavicon) }}"
    data-dark-href="{{ asset($darkFavicon) }}"
    type="image/x-icon"
>
