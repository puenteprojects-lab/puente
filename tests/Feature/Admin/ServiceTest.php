<?php

use App\Models\Service;
use App\Models\User;

function admin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function payload(array $overrides = []): array
{
    return array_replace_recursive([
        'slug' => 'anxiety-and-stress',
        'position' => 0,
        'is_published' => true,
        'translations' => [
            'en' => ['title' => 'Anxiety and stress', 'text' => 'Constant tension.'],
            'es' => ['title' => null, 'text' => null],
            'ru' => ['title' => null, 'text' => null],
            'uk' => ['title' => null, 'text' => null],
            'pl' => ['title' => null, 'text' => null],
        ],
    ], $overrides);
}

it('keeps guests out of the service admin', function () {
    $this->get('/admin/services')->assertRedirect('/login');
});

it('refuses a signed-in user who is not an admin', function () {
    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get('/admin/services')
        ->assertForbidden();
});

it('lists services for an admin', function () {
    Service::factory()->create(['slug' => 'burnout']);

    $this->actingAs(admin())
        ->get('/admin/services')
        ->assertOk();
});

it('creates a service', function () {
    $this->actingAs(admin())
        ->post('/admin/services', payload())
        ->assertRedirect('/admin/services');

    $service = Service::firstWhere('slug', 'anxiety-and-stress');

    expect($service)->not->toBeNull()
        ->and($service->translations['en']['title'])->toBe('Anxiety and stress')
        ->and($service->is_published)->toBeTrue();
});

it('requires wording in the base language', function () {
    $this->actingAs(admin())
        ->post('/admin/services', payload([
            'translations' => ['en' => ['title' => null, 'text' => null]],
        ]))
        ->assertSessionHasErrors(['translations.en.title', 'translations.en.text']);
});

it('allows the other languages to be left empty', function () {
    $this->actingAs(admin())
        ->post('/admin/services', payload())
        ->assertSessionHasNoErrors();

    expect(Service::firstWhere('slug', 'anxiety-and-stress')->missingLocales())
        ->toBe(['es', 'ru', 'uk', 'pl']);
});

it('rejects a slug that is not url safe', function () {
    $this->actingAs(admin())
        ->post('/admin/services', payload(['slug' => 'Anxiety & Stress']))
        ->assertSessionHasErrors('slug');
});

it('rejects a duplicate slug but lets a service keep its own', function () {
    $service = Service::factory()->create(['slug' => 'burnout']);

    $this->actingAs(admin())
        ->post('/admin/services', payload(['slug' => 'burnout']))
        ->assertSessionHasErrors('slug');

    $this->actingAs(admin())
        ->put("/admin/services/{$service->id}", payload(['slug' => 'burnout']))
        ->assertSessionHasNoErrors();
});

it('updates a service', function () {
    $service = Service::factory()->create(['slug' => 'burnout']);

    $this->actingAs(admin())
        ->put("/admin/services/{$service->id}", payload([
            'slug' => 'burnout',
            'translations' => ['en' => ['title' => 'Burnout', 'text' => 'No energy left.']],
        ]))
        ->assertRedirect('/admin/services');

    expect($service->refresh()->translations['en']['title'])->toBe('Burnout');
});

it('deletes a service', function () {
    $service = Service::factory()->create();

    $this->actingAs(admin())
        ->delete("/admin/services/{$service->id}")
        ->assertRedirect('/admin/services');

    expect(Service::find($service->id))->toBeNull();
});

it('saves a new running order', function () {
    $first = Service::factory()->create(['position' => 0]);
    $second = Service::factory()->create(['position' => 1]);

    $this->actingAs(admin())
        ->post('/admin/services/reorder', ['ids' => [$second->id, $first->id]])
        ->assertSessionHasNoErrors();

    expect($second->refresh()->position)->toBe(0)
        ->and($first->refresh()->position)->toBe(1);
});
