<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Recreate the partial unique SKU index so a soft-deleted item frees its SKU for reuse.
        DB::statement('DROP INDEX IF EXISTS items_sku_unique');
        DB::statement('CREATE UNIQUE INDEX items_sku_unique ON items (sku) WHERE sku IS NOT NULL AND deleted_at IS NULL');

        // Every item query filters on deleted_at (SoftDeletes) and the list defaults to
        // ORDER BY created_at, id — back both with a composite index.
        DB::statement('CREATE INDEX IF NOT EXISTS items_deleted_at_created_at_id_index ON items (deleted_at, created_at, id)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS items_deleted_at_created_at_id_index');
        DB::statement('DROP INDEX IF EXISTS items_sku_unique');
        DB::statement('CREATE UNIQUE INDEX items_sku_unique ON items (sku) WHERE sku IS NOT NULL');
    }
};
