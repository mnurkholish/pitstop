<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function services(Request $request): View
    {
        return view('public.services.index', [
            'services' => $this->filteredServices($request)->get(),
        ]);
    }

    public function searchServices(Request $request): JsonResponse
    {
        $services = $this->filteredServices($request)->get();

        return response()->json([
            'count' => $services->count(),
            'cards' => view('public.services.partials.cards', compact('services'))->render(),
            'empty' => view('public.services.partials.empty')->render(),
        ]);
    }

    public function showService(Service $service): JsonResponse
    {
        abort_unless($service->is_active, 404);

        return response()->json([
            'service' => [
                'name' => $service->name,
                'description' => $service->description ?: 'Belum ada deskripsi layanan.',
                'price' => 'Rp '.number_format($service->price, 0, ',', '.'),
                'duration_minutes' => $service->duration_minutes,
                'image_url' => $service->image
                    ? asset(Str::startsWith($service->image, 'images/') ? $service->image : 'storage/'.$service->image)
                    : asset('images/services/service-default.png'),
            ],
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    private function filteredServices(Request $request): Builder
    {
        $search = trim((string) $request->string('search'));

        return Service::query()
            ->where('is_active', true)
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->orderBy('name');
    }
}
