<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve the active locale for the request.
     *
     * The landing routes state their own language and must be taken at their
     * word: a prefixed URL through its route parameter, and the root as the
     * base locale. That is what the canonical and hreflang tags promise, and
     * it is the only way the switcher can lead back to the base language once
     * a visitor has picked another one.
     *
     * Everywhere else — the dashboard, settings, the admin — there is no
     * language in the URL, so we fall back to the visitor's last choice and
     * then to what their browser asks for.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported'));
        $base = config('locales.base');

        $route = $request->route();

        if ($route?->named('home')) {
            $locale = $base;
        } else {
            $locale = $route?->parameter('locale');

            if (! in_array($locale, $supported, true)) {
                $locale = $request->session()->get('locale');
            }

            if (! in_array($locale, $supported, true)) {
                $locale = $request->getPreferredLanguage($supported);
            }

            if (! in_array($locale, $supported, true)) {
                $locale = $base;
            }
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
