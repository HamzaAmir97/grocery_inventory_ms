<?php

use App\Support\ServiceIdentity;

it('serves a branded html landing page at the base address', function () {
    config(['app.name' => ServiceIdentity::NAME]);

    $response = $this->get('/');

    $response->assertSuccessful()
        ->assertSee(ServiceIdentity::NAME)
        ->assertSee('/api/documentation')
        ->assertSee('/api/status')
        ->assertSee('POST /api/auth/login')
        ->assertDontSee('Laravel News')
        ->assertDontSee('Documentation and Laracasts');
});

it('serves a json pointer for api clients at the base address', function () {
    config(['app.name' => ServiceIdentity::NAME]);

    $response = $this->getJson('/');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Grocery Inventory Management API.')
        ->assertJsonPath('data.documentation_url', url('/api/documentation'))
        ->assertJsonPath('data.status_url', url('/api/status'))
        ->assertJsonPath('data.authentication.login_path', '/api/auth/login')
        ->assertJsonPath('data.service.name', ServiceIdentity::name())
        ->assertJsonPath('data.service.version', ServiceIdentity::version())
        ->assertJsonPath('data.service.environment', ServiceIdentity::environment());

    expect($response->json('data.documentation_url'))->toStartWith('http')
        ->and($response->json('data.status_url'))->toStartWith('http');
});
