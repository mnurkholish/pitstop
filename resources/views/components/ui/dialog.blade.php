@props([
    'name',
    'title',
    'description' => null,
    'show' => false,
    'variant' => 'warning',
])

<x-ui.modal :name="$name" :title="$title" :show="$show" max-width="sm">
    @if ($description)
        <p class="text-sm leading-6 text-slate-600">{{ $description }}</p>
    @endif
    {{ $slot }}

    @isset($footer)
        <x-slot name="footer">{{ $footer }}</x-slot>
    @endisset
</x-ui.modal>
