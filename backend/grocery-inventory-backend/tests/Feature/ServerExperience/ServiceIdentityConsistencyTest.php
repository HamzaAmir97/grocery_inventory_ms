<?php

use App\Support\ServiceIdentity;
use Illuminate\Support\Facades\Artisan;

function serverExperienceOpenApiDocument(): array
{
    Artisan::call('l5-swagger:generate');

    return json_decode(
        file_get_contents(storage_path('api-docs/api-docs.json')),
        true,
        flags: JSON_THROW_ON_ERROR
    );
}

it('resolves configured service identity with safe defaults', function () {
    config([
        'app.name' => ServiceIdentity::NAME,
        'app.version' => ServiceIdentity::VERSION,
    ]);

    expect(ServiceIdentity::name())->toBe(ServiceIdentity::NAME)
        ->and(ServiceIdentity::version())->toBe(ServiceIdentity::VERSION)
        ->and(ServiceIdentity::environment())->toBe(app()->environment());
});

it('keeps landing and status identity in sync', function () {
    config([
        'app.name' => ServiceIdentity::NAME,
        'app.version' => ServiceIdentity::VERSION,
    ]);

    $landingService = $this->getJson('/')->assertSuccessful()->json('data.service');
    $statusService = $this->getJson('/api/status')->assertSuccessful()->json('data.service');

    expect($landingService)->toBe($statusService);
});

it('keeps generated documentation identity in sync with service identity', function () {
    $document = serverExperienceOpenApiDocument();

    expect($document['info']['title'] ?? null)->toBe(ServiceIdentity::NAME)
        ->and($document['info']['version'] ?? null)->toBe(ServiceIdentity::VERSION);
});

it('returns status timestamps as iso 8601 strings', function () {
    $checkedAt = $this->getJson('/api/status')->assertSuccessful()->json('data.checked_at');

    expect($checkedAt)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?[+-]\d{2}:\d{2}$/');
});
