<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $base = config('locales.base');
        $locale = app()->getLocale();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'i18n' => [
                'locale' => $locale,
                'base' => $base,
                // The switcher needs the URL for each language, and the base
                // locale lives at the root rather than behind a prefix.
                'locales' => collect(config('locales.supported'))
                    ->map(fn (array $meta, string $code) => [
                        'code' => $code,
                        'native' => $meta['native'],
                        'url' => $code === $base ? '/' : "/{$code}",
                        'current' => $code === $locale,
                    ])
                    ->values()
                    ->all(),
                'messages' => trans('landing'),
            ],
        ];
    }
}
