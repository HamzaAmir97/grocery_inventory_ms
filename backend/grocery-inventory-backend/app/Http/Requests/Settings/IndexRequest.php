<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\Concerns\TrimStrings;
use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IndexRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['sometimes', 'string', Rule::in([
                'name',
                'description',
                'is_active',
                'subcategories_count',
                'items_count',
                'category',
                'symbol',
                'contact_person',
                'phone',
                'email',
                'created_at',
            ])],
            'sort_dir' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();

        // Soft-cap per_page to keep the existing UX (silent cap at 100).
        if ($this->has('per_page')) {
            $value = (int) $this->input('per_page');
            if ($value > 100) {
                $this->merge(['per_page' => 100]);
            }
        }
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(ApiResponse::validation($validator->errors()->toArray()));
    }

    public function searchTerm(): string
    {
        return (string) $this->input('search', '');
    }

    public function perPage(): int
    {
        return max(1, min(100, (int) $this->input('per_page', 10)));
    }

    /**
     * @param  array<int, string>  $allowed
     *
     * @throws ValidationException
     */
    public function sortBy(array $allowed = [], string $fallback = 'id'): string
    {
        $value = (string) $this->input('sort_by', $fallback);

        if ($value !== $fallback && $allowed !== [] && ! in_array($value, $allowed, true)) {
            throw ValidationException::withMessages([
                'sort_by' => ['The selected sort by is invalid.'],
            ]);
        }

        return $value;
    }

    public function sortDirection(string $fallback = 'asc'): string
    {
        return (string) $this->input('sort_dir', $fallback);
    }

    public function pageNumber(): int
    {
        return max(1, (int) $this->input('page', 1));
    }
}
