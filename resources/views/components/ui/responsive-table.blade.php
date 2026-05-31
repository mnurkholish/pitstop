<div {{ $attributes }}>
    <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                {{ $desktop }}
            </table>
        </div>
    </div>

    <div class="space-y-3 md:hidden">
        {{ $mobile }}
    </div>
</div>
