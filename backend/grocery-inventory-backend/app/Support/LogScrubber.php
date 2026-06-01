<?php

namespace App\Support;

class LogScrubber
{
    private const REDACTED_KEYS = [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'authorization',
        'secret',
        'api_key',
        'jwt',
    ];

    private const PLACEHOLDER = '[REDACTED]';

    /**
     * Recursively redact sensitive keys from any payload before logging.
     *
     * @param  array<mixed>  $payload
     * @return array<mixed>
     */
    public static function redact(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::REDACTED_KEYS, true)) {
                $payload[$key] = self::PLACEHOLDER;

                continue;
            }

            if (is_array($value)) {
                $payload[$key] = self::redact($value);
            }
        }

        return $payload;
    }
}
