<?php

namespace App\Support;

class ServiceIdentity
{
    public const NAME = 'Grocery Inventory Management API';

    public const VERSION = '1.0.0';

    public static function name(): string
    {
        return (string) config('app.name', self::NAME);
    }

    public static function version(): string
    {
        return (string) config('app.version', self::VERSION);
    }

    public static function environment(): string
    {
        return app()->environment();
    }

    /**
     * @return array{name: string, version: string, environment: string}
     */
    public static function toArray(): array
    {
        return [
            'name' => self::name(),
            'version' => self::version(),
            'environment' => self::environment(),
        ];
    }
}
