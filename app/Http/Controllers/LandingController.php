<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'services' => $this->services(),
        ]);
    }

    /**
     * The service cards for the active language.
     *
     * Returns an empty list when nothing is published, and the page then falls
     * back to the wording in lang/{locale}/landing.php — so the section never
     * renders blank, whether or not the database has been seeded.
     *
     * @return list<array{title: string, text: string}>
     */
    private function services(): array
    {
        $locale = app()->getLocale();

        return Service::published()
            ->ordered()
            ->get()
            ->map(fn (Service $service) => $service->forLocale($locale))
            ->all();
    }
}
