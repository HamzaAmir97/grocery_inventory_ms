<?php

namespace App\Http\Requests\Settings;

use App\Http\Requests\Concerns\TrimStrings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class SupplierRequest extends FormRequest
{
    use TrimStrings;

    public function authorize(): bool
    {
        return true;
    }

    protected function nameUniqueRule(): Unique
    {
        return Rule::unique('suppliers', 'name')->ignore($this->route('supplier')?->id);
    }

    protected function prepareForValidation(): void
    {
        $this->trimAllStrings();
    }
}
