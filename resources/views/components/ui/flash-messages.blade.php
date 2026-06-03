@php
    $messages = collect([
        'success' => ['variant' => 'success', 'value' => session('success')],
        'error' => ['variant' => 'danger', 'value' => session('error')],
        'warning' => ['variant' => 'warning', 'value' => session('warning')],
        'info' => ['variant' => 'info', 'value' => session('info')],
    ])->filter(fn ($message) => filled($message['value']));
@endphp

@if ($messages->isNotEmpty())
    <div class="pitstop-container pt-5">
        <div class="space-y-3">
            @foreach ($messages as $message)
                <x-ui.alert :variant="$message['variant']">{{ $message['value'] }}</x-ui.alert>
            @endforeach
        </div>
    </div>
@endif
