@extends('layouts.app')

@section('body_class', 'font-sans text-slate-700 antialiased')

@section('page')
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-8">
        <div>
            <a href="/">
                <x-ui.logo class="text-lg" />
            </a>
        </div>

        <div class="mt-6 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white px-6 py-6 shadow-sm sm:max-w-md">
            {{ $slot }}
        </div>
    </div>
@endsection
