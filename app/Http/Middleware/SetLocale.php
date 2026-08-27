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
     * The URL prefix wins, because a shared link must always open in the
     * language it was shared in. Otherwise we fall back to what the visitor
     * chose earlier, then to the browser's preference.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported'));

        $locale = $request->route('locale');

        if (! in_array($locale, $supported, true)) {
            $locale = $request->session()->get('locale');
        }

        if (! in_array($locale, $supported, true)) {
            $locale = $request->getPreferredLanguage($supported);
        }

        if (! in_array($locale, $supported, true)) {
            $locale = config('locales.base');
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
