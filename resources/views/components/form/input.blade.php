@props([
    'label' => null,
    'name',
    'type' => 'text',
    'required' => false,
    'value' => null,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @required($required)
        {{ $attributes->class([
            'block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500',
            'border-red-400 focus:border-red-500 focus:ring-red-500' => $errors->has($name),
        ]) }}
    >
    <x-form.error :name="$name" />
</div>
