<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\Concerns\TrimStrings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class CategoryRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    protected function nameUniqueRule(): Unique
    {
        $id = $this->route('category')?->id;

        return Rule::unique('categories', 'name')->ignore($id);
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }
}
