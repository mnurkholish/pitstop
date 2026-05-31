@foreach ($services as $service)
    <x-ui.service-card :service="$service" detail />
@endforeach
