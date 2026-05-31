<button
    type="button"
    @click="open = ! open"
    class="inline-flex size-10 items-center justify-center rounded-lg text-slate-600 transition hover:bg-slate-100 md:hidden"
    :aria-expanded="open"
    aria-label="Buka menu navigasi"
>
    <svg x-show="! open" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <svg x-show="open" x-cloak class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
    </svg>
</button>
