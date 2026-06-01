<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateUnitRequest extends UnitRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', $this->nameUniqueRule()],
            'symbol' => ['sometimes', 'required', 'string', 'max:50', $this->symbolUniqueRule()],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
