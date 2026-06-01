<?php

use Illuminate\Support\Facades\DB;

it('contains the documented read pattern indexes', function (string $table, array $indexes) {
    $actualIndexes = collect(DB::select(
        'SELECT indexname FROM pg_indexes WHERE schemaname = ? AND tablename = ?',
        ['public', $table]
    ))->pluck('indexname');

    foreach ($indexes as $index) {
        expect($actualIndexes)->toContain($index);
    }
})->with([
    'items' => ['items', [
        'items_name_index',
        'items_sku_unique',
        'items_category_id_index',
        'items_subcategory_id_index',
        'items_unit_id_index',
        'items_supplier_id_index',
        'items_stock_quantity_index',
    ]],
    'subcategories' => ['subcategories', [
        'subcategories_category_id_index',
    ]],
]);
