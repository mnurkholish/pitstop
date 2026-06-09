@extends('layouts.app')

@isset($header)
    @section('header')
        {{ $header }}
    @endsection
@endisset

@section('navbar')
    <x-navbar.user />
@endsection

@section('page')
    {{ $slot }}
@endsection
