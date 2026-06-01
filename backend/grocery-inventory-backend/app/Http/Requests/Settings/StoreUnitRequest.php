<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;

class StoreUnitRequest extends UnitRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->nameUniqueRule()],
            'symbol' => ['required', 'string', 'max:50', $this->symbolUniqueRule()],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
