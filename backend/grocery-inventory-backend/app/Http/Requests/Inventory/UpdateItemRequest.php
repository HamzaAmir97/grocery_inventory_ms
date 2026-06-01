<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateItemRequest extends ItemRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->itemRules(['sometimes', 'required'], ['sometimes'], allowVersion: true);
    }
}
