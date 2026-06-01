<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\Concerns\TrimStrings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class UnitRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    protected function nameUniqueRule(): Unique
    {
        return Rule::unique('units', 'name')->ignore($this->route('unit')?->id);
    }

    protected function symbolUniqueRule(): Unique
    {
        return Rule::unique('units', 'symbol')->ignore($this->route('unit')?->id);
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }
}
