@extends('layouts.app')

@isset($header)
    @section('header')
        {{ $header }}
    @endsection
@endisset

@section('navbar')
    <x-navbar.admin />
@endsection

@section('page')
    {{ $slot }}
@endsection
