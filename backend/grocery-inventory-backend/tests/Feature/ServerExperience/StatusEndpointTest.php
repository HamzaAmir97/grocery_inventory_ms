<?php

use Illuminate\Database\DatabaseManager;

it('serves public service status without credentials', function () {
    $response = $this->getJson('/api/status');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Service status retrieved.')
        ->assertJsonPath('data.status', 'ok')
        ->assertJsonPath('data.dependencies.database', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'status',
                'service' => ['name', 'version', 'environment'],
                'dependencies' => ['database'],
                'checked_at',
            ],
        ]);

    expect($response->json('data.service.name'))->not->toBeEmpty()
        ->and($response->json('data.service.version'))->not->toBeEmpty()
        ->and($response->json('data.service.environment'))->not->toBeEmpty();
});

it('reports degraded status when the database probe fails', function () {
    $database = Mockery::mock(DatabaseManager::class);
    $database->shouldReceive('connection')->once()->andThrow(new RuntimeException('database unavailable'));

    $this->app->instance(DatabaseManager::class, $database);

    $this->getJson('/api/status')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.status', 'degraded')
        ->assertJsonPath('data.dependencies.database', false);
});

it('does not leak secret-bearing keys or diagnostics from status', function () {
    $body = $this->getJson('/api/status')->assertSuccessful()->getContent();

    foreach (['password', 'DB_', 'APP_KEY', 'JWT_SECRET', 'stack trace', 'Trace:', 'vendor\\'] as $needle) {
        expect($body)->not->toContain($needle);
    }
});
