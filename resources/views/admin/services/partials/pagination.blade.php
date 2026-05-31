@if ($services->hasPages())
    <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
        <p class="text-slate-500">Halaman {{ $services->currentPage() }} dari {{ $services->lastPage() }}</p>
        <div class="flex gap-2">
            @if ($services->onFirstPage())
                <span class="rounded-lg border border-slate-200 px-3 py-2 text-slate-300">Sebelumnya</span>
            @else
                <button type="button" @click="fetchServices(@js($services->previousPageUrl()))" class="rounded-lg border border-slate-300 px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50">Sebelumnya</button>
            @endif
            @if ($services->hasMorePages())
                <button type="button" @click="fetchServices(@js($services->nextPageUrl()))" class="rounded-lg border border-slate-300 px-3 py-2 font-medium text-slate-600 transition hover:bg-slate-50">Berikutnya</button>
            @else
                <span class="rounded-lg border border-slate-200 px-3 py-2 text-slate-300">Berikutnya</span>
            @endif
        </div>
    </div>
@endif
