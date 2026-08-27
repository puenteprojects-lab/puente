<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $service = $this->route('service');
        $base = config('locales.base');

        $rules = [
            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                Rule::unique('services', 'slug')->ignore($service),
            ],
            'position' => ['required', 'integer', 'min:0', 'max:1000'],
            'is_published' => ['required', 'boolean'],
            'translations' => ['required', 'array'],
        ];

        foreach (array_keys(config('locales.supported')) as $locale) {
            // The base locale is what every other language falls back to, so
            // it has to be filled in; the rest may be finished later.
            $required = $locale === $base ? 'required' : 'nullable';

            $rules["translations.{$locale}"] = ['required', 'array'];
            $rules["translations.{$locale}.title"] = [$required, 'string', 'max:120'];
            $rules["translations.{$locale}.text"] = [$required, 'string', 'max:600'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => __('The slug may only contain lowercase letters, numbers and single hyphens.'),
        ];
    }
}
