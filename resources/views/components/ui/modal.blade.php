@props([
    'name',
    'title' => null,
    'show' => false,
    'maxWidth' => 'lg',
])

<x-modal :name="$name" :show="$show" :max-width="$maxWidth" {{ $attributes }}>
    <div class="flex max-h-[90vh] flex-col">
        @if ($title)
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4 sm:px-6">
                <h2 class="text-base font-semibold text-slate-800">{{ $title }}</h2>
                <button type="button" x-on:click="$dispatch('close')" class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup dialog">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
        <div class="overflow-y-auto p-4 sm:p-6">{{ $slot }}</div>
        @isset($footer)
            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
                {{ $footer }}
            </div>
        @endisset
    </div>
</x-modal>
