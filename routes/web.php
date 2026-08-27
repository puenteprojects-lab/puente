<?php

use Illuminate\Support\Facades\Route;

$locales = array_keys(config('locales.supported'));

// English is the base locale and is served from the root without a prefix.
Route::inertia('/', 'Welcome')->name('home');

// Every other locale gets its own prefix so a shared link keeps its language.
Route::get('/{locale}', fn () => inertia('Welcome'))
    ->whereIn('locale', $locales)
    ->name('home.locale');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
