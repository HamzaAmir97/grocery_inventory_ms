<?php

use App\Models\Category;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Route;

function assertPolishFailureEnvelope($response, int $status, string $message, bool $hasErrors = false): void
{
    $response
        ->assertStatus($status)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', $message)
        ->assertJsonMissingPath('data');

    if ($hasErrors) {
        $response->assertJsonStructure(['errors']);
    }
}

it('returns predictable envelopes for common recoverable api failures', function () {
    $headers = settingsAuthHeaders($this);

    assertPolishFailureEnvelope(
        $this->get('/api/dashboard/stats'),
        401,
        'Unauthenticated.',
    );

    $this->withHeaders($headers)->postJson('/api/items', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed.')
        ->assertJsonStructure(['errors' => ['name', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'price', 'stock_quantity']])
        ->assertJsonMissingPath('data');

    assertPolishFailureEnvelope(
        $this->get('/api/unknown-polish-path'),
        404,
        'Unknown Polish Path not found.',
    );

    assertPolishFailureEnvelope(
        $this->withHeaders($headers)->getJson('/api/items/999999'),
        404,
        'Item not found.',
    );

    $dairy = Category::query()->where('name', 'Dairy')->sole();

    assertPolishFailureEnvelope(
        $this->withHeaders($headers)->deleteJson("/api/categories/{$dairy->id}"),
        409,
        'This category still has subcategories.',
    );

    Route::middleware('auth:api')->post('/api/polish-duplicate-category', function () {
        Category::query()->create(['name' => 'Dairy']);
    });

    assertPolishFailureEnvelope(
        $this->withHeaders($headers)->postJson('/api/polish-duplicate-category'),
        409,
        'A record with these details already exists.',
    );

    assertPolishFailureEnvelope(
        $this->withHeaders($headers)->postJson('/api/dashboard/stats'),
        405,
        'Method not allowed.',
    );
});

it('returns a generic server envelope while privately reporting unexpected failures', function () {
    $headers = settingsAuthHeaders($this);
    $exception = new RuntimeException('password token .env select * from sensitive_table D:\\private\\file.php');

    $this->app->bind(DashboardService::class, fn () => new class($exception) extends DashboardService
    {
        public function __construct(private readonly RuntimeException $exception) {}

        public function summary(): array
        {
            throw $this->exception;
        }
    });

    Exceptions::fake();

    $response = $this->withHeaders($headers)->getJson('/api/dashboard/stats');

    assertPolishFailureEnvelope($response, 500, 'Server error.');
    expect($response->getContent())->not->toContain('trace')
        ->not->toContain('file')
        ->not->toContain('select *')
        ->not->toContain('token')
        ->not->toContain('password')
        ->not->toContain('.env');

    Exceptions::assertReported(RuntimeException::class);
});

it('does not expose database details for non-user-correctable query failures', function () {
    $headers = settingsAuthHeaders($this);

    Route::middleware('auth:api')->get('/api/polish-query-failure', function () {
        DB::statement('select * from definitely_missing_polish_table where password = ?', ['token-value']);
    });

    $response = $this->withHeaders($headers)->getJson('/api/polish-query-failure');

    assertPolishFailureEnvelope($response, 500, 'Server error.');
    expect($response->getContent())->not->toContain('definitely_missing_polish_table')
        ->not->toContain('select *')
        ->not->toContain('password')
        ->not->toContain('token-value');
});
