<?php

use App\Models\Service;
use Inertia\Testing\AssertableInertia;

it('serves the base locale from the root', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Welcome')
            ->where('i18n.locale', 'en'));
});

it('serves each language behind its own prefix', function (string $locale) {
    $this->get("/{$locale}")
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->where('i18n.locale', $locale));
})->with(['es', 'ru', 'uk', 'pl']);

it('does not treat an unknown prefix as a locale', function () {
    $this->get('/de')->assertNotFound();
});

it('keeps the root on the base locale after another language was visited', function () {
    // The canonical and hreflang tags promise that / is English, and the
    // switcher has to be able to lead back to it.
    $this->get('/ru')->assertInertia(fn (AssertableInertia $page) => $page->where('i18n.locale', 'ru'));

    $this->get('/')->assertInertia(fn (AssertableInertia $page) => $page->where('i18n.locale', 'en'));
});

it('remembers the chosen language on pages that have no prefix', function () {
    $this->get('/uk');

    $this->get('/login')
        ->assertInertia(fn (AssertableInertia $page) => $page->where('i18n.locale', 'uk'));
});

it('passes published services to the page in the active language', function () {
    Service::factory()->create([
        'slug' => 'relationships',
        'position' => 0,
        'translations' => [
            'en' => ['title' => 'Relationships', 'text' => 'Conflict with a partner.'],
            'ru' => ['title' => 'Отношения', 'text' => 'Конфликты в паре.'],
        ],
    ]);

    $this->get('/ru')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('services.0.title', 'Отношения')
            ->where('services.0.text', 'Конфликты в паре.'));
});

it('falls back to the base language for a service that is not translated yet', function () {
    Service::factory()->create([
        'translations' => [
            'en' => ['title' => 'Burnout', 'text' => 'No energy left.'],
        ],
    ]);

    $this->get('/pl')
        ->assertInertia(fn (AssertableInertia $page) => $page->where('services.0.title', 'Burnout'));
});

it('leaves out services that are not published', function () {
    Service::factory()->unpublished()->create();

    $this->get('/')
        ->assertInertia(fn (AssertableInertia $page) => $page->where('services', []));
});

it('orders services by position', function () {
    Service::factory()->create(['slug' => 'second', 'position' => 5, 'translations' => [
        'en' => ['title' => 'Second', 'text' => '...'],
    ]]);
    Service::factory()->create(['slug' => 'first', 'position' => 1, 'translations' => [
        'en' => ['title' => 'First', 'text' => '...'],
    ]]);

    $this->get('/')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('services.0.title', 'First')
            ->where('services.1.title', 'Second'));
});
