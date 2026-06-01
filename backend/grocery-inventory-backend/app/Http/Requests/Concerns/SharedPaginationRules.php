<?php

namespace App\Http\Requests\Concerns;

trait SharedPaginationRules
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function paginationRules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
