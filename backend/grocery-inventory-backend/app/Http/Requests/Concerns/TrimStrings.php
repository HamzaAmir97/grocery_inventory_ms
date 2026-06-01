<?php

namespace App\Http\Requests\Concerns;

trait TrimStrings
{
    /**
     * Trim every string in the input. Empty strings become null.
     * Call this from prepareForValidation() in FormRequests.
     */
    protected function trimAllStrings(): void
    {
        $this->merge($this->normalize($this->all()));
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function normalize(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                $input[$key] = $trimmed === '' ? null : $trimmed;
            } elseif (is_array($value)) {
                $input[$key] = $this->normalize($value);
            }
        }

        return $input;
    }
}
