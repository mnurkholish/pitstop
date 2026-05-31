@props(['service' => null])

<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input label="Nama Layanan" name="name" :value="$service?->name" required minlength="3" maxlength="255" />
    <x-form.input label="Harga" name="price" type="number" :value="$service?->price" required min="0" step="1" />
    <x-form.input label="Durasi (menit)" name="duration_minutes" type="number" :value="$service?->duration_minutes" required min="1" step="1" />
    <div>
        <label for="is_active" class="mb-1.5 block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
        <select id="is_active" name="is_active" class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            <option value="1" @selected(old('is_active', $service?->is_active ?? true) == true)>Aktif</option>
            <option value="0" @selected(old('is_active', $service?->is_active ?? true) == false)>Nonaktif</option>
        </select>
        <x-form.error name="is_active" />
    </div>
</div>
<div class="mt-5">
    <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
    <textarea id="description" name="description" rows="5" class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Jelaskan layanan bengkel">{{ old('description', $service?->description) }}</textarea>
    <x-form.error name="description" />
</div>
<x-ui.alert variant="info" class="mt-5">Upload gambar masih ditunda. Sistem menggunakan fallback logo PitStop jika gambar belum tersedia.</x-ui.alert>
