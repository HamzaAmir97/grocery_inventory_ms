<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Requests\Inventory\StoreItemRequest;
use App\Http\Requests\Inventory\UpdateItemRequest;
use App\Http\Requests\Settings\StoreCategoryRequest;
use App\Http\Requests\Settings\StoreSubcategoryRequest;
use App\Http\Requests\Settings\StoreSupplierRequest;
use App\Http\Requests\Settings\StoreUnitRequest;
use App\Http\Requests\Settings\UpdateCategoryRequest;
use App\Http\Requests\Settings\UpdateSubcategoryRequest;
use App\Http\Requests\Settings\UpdateSupplierRequest;
use App\Http\Requests\Settings\UpdateUnitRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;

function controllerParameterType(string $controller, string $method, int $index = 0): string
{
    $type = (new ReflectionMethod($controller, $method))->getParameters()[$index]->getType();

    return $type->getName();
}

it('uses separate form request classes for store and update actions', function () {
    expect(controllerParameterType(CategoryController::class, 'store'))->toBe(StoreCategoryRequest::class)
        ->and(controllerParameterType(CategoryController::class, 'update'))->toBe(UpdateCategoryRequest::class)
        ->and(controllerParameterType(SubcategoryController::class, 'store'))->toBe(StoreSubcategoryRequest::class)
        ->and(controllerParameterType(SubcategoryController::class, 'update'))->toBe(UpdateSubcategoryRequest::class)
        ->and(controllerParameterType(UnitController::class, 'store'))->toBe(StoreUnitRequest::class)
        ->and(controllerParameterType(UnitController::class, 'update'))->toBe(UpdateUnitRequest::class)
        ->and(controllerParameterType(SupplierController::class, 'store'))->toBe(StoreSupplierRequest::class)
        ->and(controllerParameterType(SupplierController::class, 'update'))->toBe(UpdateSupplierRequest::class)
        ->and(controllerParameterType(ItemController::class, 'store'))->toBe(StoreItemRequest::class)
        ->and(controllerParameterType(ItemController::class, 'update'))->toBe(UpdateItemRequest::class);
});

it('keeps store fields required while allowing partial settings updates', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/categories', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['name']]);

    $this->withHeaders($headers)->postJson('/api/subcategories', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['category_id', 'name']]);

    $this->withHeaders($headers)->postJson('/api/units', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['name', 'symbol']]);

    $this->withHeaders($headers)->postJson('/api/suppliers', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['name']]);

    $category = Category::query()->where('name', 'Dairy')->sole();
    $subcategory = Subcategory::query()->where('category_id', $category->id)->where('name', 'Milk')->sole();
    $unit = Unit::query()->where('name', 'Liter')->sole();
    $supplier = Supplier::query()->where('name', 'Daily Dairy Co')->sole();

    $this->withHeaders($headers)->putJson("/api/categories/{$category->id}", ['description' => 'Partial category update'])
        ->assertSuccessful()
        ->assertJsonPath('data.description', 'Partial category update');

    $this->withHeaders($headers)->putJson("/api/subcategories/{$subcategory->id}", ['description' => 'Partial subcategory update'])
        ->assertSuccessful()
        ->assertJsonPath('data.description', 'Partial subcategory update');

    $this->withHeaders($headers)->putJson("/api/units/{$unit->id}", ['is_active' => false])
        ->assertSuccessful()
        ->assertJsonPath('data.is_active', false);

    $this->withHeaders($headers)->putJson("/api/suppliers/{$supplier->id}", ['phone' => '555-0101'])
        ->assertSuccessful()
        ->assertJsonPath('data.phone', '555-0101');
});

it('requires full item store payloads and permits partial item updates', function () {
    $headers = settingsAuthHeaders($this);

    $this->withHeaders($headers)->postJson('/api/items', [])
        ->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['name', 'category_id', 'subcategory_id', 'unit_id', 'supplier_id', 'price', 'stock_quantity']]);

    $this->withHeaders($headers)->postJson('/api/items', inventoryPayload([
        'name' => 'Version On Store',
        'sku' => 'VERSION-ON-STORE',
        'version' => 1,
    ]))->assertUnprocessable()
        ->assertJsonStructure(['errors' => ['version']]);

    $item = Item::query()->firstOrFail();

    $this->withHeaders($headers)->putJson("/api/items/{$item->id}", [
        'price' => 4.75,
        'version' => $item->version,
    ])->assertSuccessful()
        ->assertJsonPath('data.price', '4.75');
});
