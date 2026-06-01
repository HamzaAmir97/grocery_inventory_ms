<?php

namespace App\Http\Requests\Settings;

use App\Models\Subcategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;

class UpdateSubcategoryRequest extends SubcategoryRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'integer', $this->categoryExistsRule()],
            'name' => ['sometimes', 'required', 'string', 'max:255', $this->nameUniqueRule()],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $subcategory = $this->route('subcategory');
                $categoryId = $this->input('category_id');

                if ($subcategory === null || $categoryId === null || $this->filled('name') || $validator->errors()->has('category_id')) {
                    return;
                }

                $duplicateExists = Subcategory::query()
                    ->where('category_id', $categoryId)
                    ->where('name', $subcategory->name)
                    ->whereKeyNot($subcategory->id)
                    ->exists();

                if ($duplicateExists) {
                    $validator->errors()->add('category_id', 'The selected category already has a subcategory with this name.');
                }
            },
        ];
    }
}
