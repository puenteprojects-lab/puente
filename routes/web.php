<?php

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

$locales = array_keys(config('locales.supported'));

// English is the base locale and is served from the root without a prefix.
Route::get('/', LandingController::class)->name('home');

// Every other locale gets its own prefix so a shared link keeps its language.
Route::get('/{locale}', LandingController::class)
    ->whereIn('locale', $locales)
    ->name('home.locale');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
        Route::get('services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });
});

require __DIR__.'/settings.php';
