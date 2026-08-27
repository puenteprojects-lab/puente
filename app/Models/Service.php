<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'translations',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'is_published' => 'boolean',
            'position' => 'integer',
        ];
    }

    /** @param  Builder<Service>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /** @param  Builder<Service>  $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /**
     * The title and text for one locale.
     *
     * Falls back to the base locale so a service that has not been translated
     * yet still shows something readable rather than an empty card.
     *
     * @return array{title: string, text: string}
     */
    public function forLocale(string $locale): array
    {
        $base = config('locales.base');

        $translated = $this->translations[$locale] ?? [];
        $fallback = $this->translations[$base] ?? [];

        return [
            'title' => $translated['title'] ?? $fallback['title'] ?? $this->slug,
            'text' => $translated['text'] ?? $fallback['text'] ?? '',
        ];
    }

    /**
     * Which locales this service still has no wording for.
     *
     * @return list<string>
     */
    public function missingLocales(): array
    {
        return collect(array_keys(config('locales.supported')))
            ->reject(fn (string $locale) => filled($this->translations[$locale]['title'] ?? null))
            ->values()
            ->all();
    }
}
