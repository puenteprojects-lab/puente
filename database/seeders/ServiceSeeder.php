<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Lang;

class ServiceSeeder extends Seeder
{
    /**
     * The slug for each service, in the order the cards were written.
     *
     * The landing copy in lang/{locale}/landing.php is the source these were
     * lifted from, so the two lists have to stay aligned by index.
     *
     * @var list<string>
     */
    private const SLUGS = [
        'anxiety-and-stress',
        'adjusting-after-a-move',
        'relationships',
        'self-worth-and-boundaries',
        'loss-and-transition',
        'burnout',
        'emotions-and-regulation',
        'eating-behaviour',
        'compulsions-and-dependency',
        'sleep-and-insomnia',
        'emotional-dependency',
        'after-a-traumatic-experience',
        'growth-work-and-money',
    ];

    public function run(): void
    {
        $locales = array_keys(config('locales.supported'));

        foreach (self::SLUGS as $index => $slug) {
            $translations = [];

            foreach ($locales as $locale) {
                $item = Lang::get('landing.services.items', [], $locale)[$index] ?? null;

                if ($item !== null) {
                    $translations[$locale] = [
                        'title' => $item['title'],
                        'text' => $item['text'],
                    ];
                }
            }

            Service::updateOrCreate(
                ['slug' => $slug],
                [
                    'translations' => $translations,
                    'position' => $index,
                    'is_published' => true,
                ],
            );
        }
    }
}
