<?php

use App\Console\Commands\InventoryServeCommand;
use Illuminate\Support\Facades\Artisan;

it('renders the branded startup panel without starting the server in preview mode', function () {
    $exitCode = Artisan::call('inventory:serve', ['--no-server' => true]);
    $output = Artisan::output();
    $plainOutput = preg_replace('/\x1b\[[0-9;]*m/', '', $output);

    expect($exitCode)->toBe(0)
        ->and($plainOutput)->toContain('I N V E N T O R Y')
        ->and($plainOutput)->toContain('Grocery Inventory Management System')
        ->and($plainOutput)->toContain('Backend · Laravel')
        ->and($plainOutput)->toContain('Local')
        ->and($plainOutput)->toContain('http://127.0.0.1:8000')
        ->and($plainOutput)->toContain('Docs')
        ->and($plainOutput)->toContain('http://127.0.0.1:8000/api/documentation')
        ->and($plainOutput)->toContain('API server starting')
        ->and($plainOutput)->toContain('Preview mode: server process was not started.');
});

it('uses custom host and port in the branded startup panel', function () {
    $exitCode = Artisan::call('inventory:serve', [
        '--host' => '0.0.0.0',
        '--port' => '8080',
        '--no-server' => true,
    ]);
    $plainOutput = preg_replace('/\x1b\[[0-9;]*m/', '', Artisan::output());

    expect($exitCode)->toBe(0)
        ->and($plainOutput)->toContain('http://0.0.0.0:8080')
        ->and($plainOutput)->toContain('http://0.0.0.0:8080/api/documentation');
});

it('keeps swagger server generation tied to the selected server url', function () {
    $command = new ReflectionClass(InventoryServeCommand::class);
    $method = $command->getMethod('prepareSwaggerForServer');
    $method->setAccessible(true);
    $instance = app(InventoryServeCommand::class);
    $instance->setLaravel(app());

    $method->invoke($instance, 'http://127.0.0.1:8123');

    $document = json_decode(
        file_get_contents(storage_path('api-docs/api-docs.json')),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    expect($document['servers'][0]['url'] ?? null)->toBe('http://127.0.0.1:8123');
});

it('provides safe local shortcut files for presentation use', function () {
    expect(file_get_contents(base_path('inventory.bat')))->toContain('php artisan inventory:serve %*')
        ->and(file_get_contents(base_path('inventory')))->toContain('php artisan inventory:serve "$@"')
        ->and(file_get_contents(base_path('inventory.bat')))->not->toContain('del ')
        ->and(file_get_contents(base_path('inventory')))->not->toContain('rm ');
});
