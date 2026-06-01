<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\Concerns\TrimStrings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

abstract class SubcategoryRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    protected function categoryExistsRule(): Exists
    {
        return Rule::exists('categories', 'id');
    }

    protected function nameUniqueRule(): Unique
    {
        $id = $this->route('subcategory')?->id;
        $categoryId = $this->input('category_id') ?? $this->route('subcategory')?->category_id;

        return Rule::unique('subcategories', 'name')
            ->where(fn ($query) => $query->where('category_id', $categoryId))
            ->ignore($id);
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }
}
