@props(['variant' => 'public'])

@if ($variant === 'admin')
    <x-navbar.admin />
@elseif ($variant === 'user')
    <x-navbar.user />
@else
    @auth
        @if (auth()->user()->role === 'admin')
            <x-navbar.admin />
        @else
            <x-navbar.user />
        @endif
    @else
        <x-navbar.guest />
    @endauth
@endif
