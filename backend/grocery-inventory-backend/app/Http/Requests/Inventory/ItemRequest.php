<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\Concerns\TrimStrings;
use App\Models\Subcategory;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ItemRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @param  array<int, string>  $required
     * @param  array<int, string>  $sometimes
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function itemRules(array $required, array $sometimes, bool $allowVersion): array
    {
        $itemId = $this->route('item')?->id;

        return [
            'name' => [...$required, 'string', 'max:255'],
            'sku' => [...$sometimes, 'nullable', 'string', 'max:64', Rule::unique('items', 'sku')->ignore($itemId)->whereNull('deleted_at')],
            'category_id' => [...$required, 'bail', 'integer', Rule::exists('categories', 'id'), $this->matchingCategoryRule()],
            'subcategory_id' => [...$required, 'bail', 'integer', Rule::exists('subcategories', 'id'), $this->matchingSubcategoryRule()],
            'unit_id' => [...$required, 'integer', Rule::exists('units', 'id')],
            'supplier_id' => [...$required, 'integer', Rule::exists('suppliers', 'id')],
            'price' => [...$required, 'numeric', 'min:0', 'lt:100000000'],
            'stock_quantity' => [...$required, 'integer', 'min:0', 'lt:1000000'],
            'low_stock_threshold' => [...$sometimes, 'nullable', 'integer', 'min:0', 'lt:1000000'],
            'description' => [...$sometimes, 'nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
            'version' => $allowVersion ? ['sometimes', 'integer', 'min:0'] : ['prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }

    private function matchingSubcategoryRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $this->filled('category_id') && $this->route('item')?->category_id === null) {
                return;
            }

            $categoryId = $this->input('category_id') ?? $this->route('item')?->category_id;

            if (! Subcategory::query()->whereKey($value)->where('category_id', $categoryId)->exists()) {
                $fail('The selected subcategory does not belong to the selected category.');
            }
        };
    }

    private function matchingCategoryRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $subcategoryId = $this->input('subcategory_id') ?? $this->route('item')?->subcategory_id;

            if ($subcategoryId !== null && ! Subcategory::query()->whereKey($subcategoryId)->where('category_id', $value)->exists()) {
                $fail('The selected subcategory does not belong to the selected category.');
            }
        };
    }
}
