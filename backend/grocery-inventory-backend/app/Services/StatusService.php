<?php

namespace App\Services;

use App\Support\ServiceIdentity;
use Illuminate\Database\DatabaseManager;
use Throwable;

class StatusService
{
    public function __construct(
        private readonly DatabaseManager $database
    ) {}

    /**
     * @return array{status: string, service: array{name: string, version: string, environment: string}, dependencies: array{database: bool}, checked_at: string}
     */
    public function report(): array
    {
        $databaseAvailable = $this->databaseAvailable();

        return [
            'status' => $databaseAvailable ? 'ok' : 'degraded',
            'service' => ServiceIdentity::toArray(),
            'dependencies' => [
                'database' => $databaseAvailable,
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    private function databaseAvailable(): bool
    {
        try {
            $this->database->connection()->getPdo();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
