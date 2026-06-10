@php
    // Preferensi tampilan global
    $theme = in_array(request()->cookie('pitstop_theme'), ['light', 'dark'], true) ? request()->cookie('pitstop_theme') : 'light';
    $fontSize = in_array(request()->cookie('pitstop_font_size'), ['normal', 'large'], true) ? request()->cookie('pitstop_font_size') : 'normal';
    $pageTitle = trim($__env->yieldContent('title')) ?: ($title ?? config('app.name', 'PitStop'));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="pitstop-theme-{{ $theme }} pitstop-font-{{ $fontSize }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle }}</title>
        <x-ui.theme-favicon />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="@yield('body_class', 'min-h-screen bg-slate-50 font-sans text-slate-700 antialiased')">
        @yield('navbar')

        @hasSection('header')
            <header class="border-b border-slate-200 bg-white">
                <div class="pitstop-container py-6">
                    @yield('header')
                </div>
            </header>
        @endif

        <main>
            <x-ui.flash-messages />
            @hasSection('page')
                @yield('page')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        @yield('footer')
        @stack('scripts')
    </body>
</html>
