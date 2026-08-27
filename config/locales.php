<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | English is the base locale and is served without a URL prefix. Every
    | other locale lives behind its own prefix, e.g. /es, /ru, /uk, /pl.
    |
    | The "native" label is what the language switcher shows, so it is written
    | in the language itself rather than translated.
    |
    */

    'base' => 'en',

    'supported' => [
        'en' => ['native' => 'English', 'html' => 'en'],
        'es' => ['native' => 'Español', 'html' => 'es'],
        'ru' => ['native' => 'Русский', 'html' => 'ru'],
        'uk' => ['native' => 'Українська', 'html' => 'uk'],
        'pl' => ['native' => 'Polski', 'html' => 'pl'],
    ],

];
