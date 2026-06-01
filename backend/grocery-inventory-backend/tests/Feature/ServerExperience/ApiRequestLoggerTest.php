<?php

use Illuminate\Support\Facades\Log;

it('logs api method url status response time ip and timestamp', function () {
    Log::shouldReceive('withContext')->andReturnNull();
    Log::shouldReceive('error')->zeroOrMoreTimes();
    Log::shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return str_contains($message, 'API GET /api/status 200 OK')
                && str_ends_with($message, 'ms')
                && $context['method'] === 'GET'
                && str_ends_with($context['url'], '/api/status')
                && $context['status'] === 200
                && is_int($context['duration_ms'])
                && array_key_exists('ip', $context)
                && preg_match('/^\d{4}-\d{2}-\d{2}T/', $context['timestamp']) === 1;
        });

    $this->getJson('/api/status')->assertSuccessful();
});

it('does not write cli log lines unless the branded server enables them', function () {
    putenv('INVENTORY_CLI_LOGS=false');

    Log::shouldReceive('withContext')->andReturnNull();
    Log::shouldReceive('error')->zeroOrMoreTimes();
    Log::shouldReceive('info')->once();

    $this->getJson('/api/status')->assertSuccessful();
});
