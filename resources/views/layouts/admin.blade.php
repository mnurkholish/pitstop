@extends('layouts.app')

@isset($header)
    @section('header')
        {{ $header }}
    @endsection
@endisset

@section('navbar')
    @include('layouts.partials.navbar', ['variant' => 'admin'])
@endsection

@section('page')
    {{ $slot }}
@endsection
