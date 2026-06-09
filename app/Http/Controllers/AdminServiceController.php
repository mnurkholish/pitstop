<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminServiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.services.index', [
            'services' => $this->filteredServices($request)->paginate(5)->withQueryString(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $services = $this->filteredServices($request)->paginate(5)->withQueryString();

        return response()->json([
            'count' => $services->total(),
            'desktop' => view('admin.services.partials.desktop-rows', compact('services'))->render(),
            'mobile' => view('admin.services.partials.mobile-cards', compact('services'))->render(),
            'pagination' => view('admin.services.partials.pagination', compact('services'))->render(),
            'empty' => $services->isEmpty(),
            'emptyHtml' => view('admin.services.partials.empty')->render(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $image = $request->file('image')?->store('services', 'public');

        if ($image) {
            $validated['image'] = $image;
        }

        try {
            Service::create($validated);
        } catch (Throwable $exception) {
            $this->deleteImage($image);

            throw $exception;
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function show(Service $service): View
    {
        return view('admin.services.show', compact('service'));
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $validated = $request->validated();
        $oldImage = $service->image;
        $newImage = $request->file('image')?->store('services', 'public');

        if ($newImage) {
            $validated['image'] = $newImage;
        } else {
            unset($validated['image']);
        }

        try {
            $service->update($validated);
        } catch (Throwable $exception) {
            $this->deleteImage($newImage);

            throw $exception;
        }

        if ($newImage && $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->bookings()->exists()) {
            return redirect()
                ->route('admin.services.index')
                ->with('error', 'Layanan sudah pernah dipakai booking dan tidak dapat dihapus. Nonaktifkan layanan jika sudah tidak tersedia.');
        }

        try {
            $service->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.services.index')
                ->with('error', 'Layanan sudah pernah dipakai booking dan tidak dapat dihapus. Nonaktifkan layanan jika sudah tidak tersedia.');
        }

        $this->deleteImage($service->image);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    private function filteredServices(Request $request): Builder
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');

        return Service::query()
            ->when($status === 'active', fn (Builder $query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn (Builder $query) => $query->where('is_active', false))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest();
    }

    private function deleteImage(?string $image): void
    {
        if ($image && ! Str::startsWith($image, 'images/')) {
            Storage::disk('public')->delete($image);
        }
    }
}
