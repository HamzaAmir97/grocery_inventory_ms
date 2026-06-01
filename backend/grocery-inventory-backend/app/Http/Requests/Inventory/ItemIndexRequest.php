<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\Concerns\TrimStrings;
use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ItemIndexRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'subcategory_id' => ['nullable', 'integer'],
            'unit_id' => ['nullable', 'integer'],
            'supplier_id' => ['nullable', 'integer'],
            'low_stock' => ['sometimes', 'boolean'],
            'sort_by' => ['sometimes', 'string', Rule::in([
                'name',
                'sku',
                'category',
                'subcategory',
                'unit',
                'supplier',
                'price',
                'stock_quantity',
                'created_at',
            ])],
            'sort_dir' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(ApiResponse::validation($validator->errors()->toArray()));
    }
}
