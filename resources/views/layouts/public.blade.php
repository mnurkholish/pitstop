@extends('layouts.app')

@section('navbar')
    @include('layouts.partials.navbar', ['variant' => 'public'])
@endsection

@section('page')
    {{ $slot }}
@endsection

@section('footer')
    @include('components.ui.footer')
@endsection
