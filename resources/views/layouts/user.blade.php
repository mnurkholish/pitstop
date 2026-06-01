@php
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true) ? request()->cookie('pitstop_theme') : 'light';
    $fontSize = in_array(request()->cookie('pitstop_font_size'), ['normal', 'large'], true) ? request()->cookie('pitstop_font_size') : 'normal';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="pitstop-theme-{{ $theme }} pitstop-font-{{ $fontSize }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'PitStop') }}</title>
        <x-ui.theme-favicon />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-700 antialiased">
        <x-navbar.user />

        @isset($header)
            <header class="border-b border-slate-200 bg-white">
                <div class="pitstop-container py-6">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
