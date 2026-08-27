<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/services/Index', [
            'services' => Service::ordered()->get()->map(fn (Service $service) => [
                'id' => $service->id,
                'slug' => $service->slug,
                'position' => $service->position,
                'is_published' => $service->is_published,
                'title' => $service->forLocale(config('locales.base'))['title'],
                'missing_locales' => $service->missingLocales(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/services/Form', [
            'service' => null,
            'nextPosition' => (int) Service::max('position') + 1,
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        Service::create($request->validated());

        return to_route('admin.services.index')
            ->with('status', __('Service created.'));
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('admin/services/Form', [
            'service' => [
                'id' => $service->id,
                'slug' => $service->slug,
                'position' => $service->position,
                'is_published' => $service->is_published,
                'translations' => $service->translations,
            ],
            'nextPosition' => $service->position,
        ]);
    }

    public function update(StoreServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return to_route('admin.services.index')
            ->with('status', __('Service updated.'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return to_route('admin.services.index')
            ->with('status', __('Service deleted.'));
    }

    /**
     * Persist a new running order after a drag or a move in the list.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:services,id'],
        ]);

        foreach ($validated['ids'] as $position => $id) {
            Service::whereKey($id)->update(['position' => $position]);
        }

        return back()->with('status', __('Order saved.'));
    }
}
