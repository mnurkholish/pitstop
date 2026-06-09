@extends('layouts.app')

@section('navbar')
    @auth
        @if (auth()->user()->role === 'admin')
            <x-navbar.admin />
        @else
            <x-navbar.user />
        @endif
    @else
        <x-navbar.guest />
    @endauth
@endsection

@section('page')
    {{ $slot }}
@endsection

@section('footer')
    @include('components.ui.footer')
@endsection
