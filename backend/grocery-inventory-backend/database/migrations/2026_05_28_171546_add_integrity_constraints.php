<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->unique('name');
        });

        Schema::table('subcategories', function (Blueprint $table): void {
            $table->unique(['category_id', 'name']);
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->unique('name');
            $table->unique('symbol');
        });

        Schema::table('suppliers', function (Blueprint $table): void {
            $table->unique('name');
        });

        DB::statement('ALTER TABLE items ADD CONSTRAINT items_price_non_negative CHECK (price >= 0)');
        DB::statement('ALTER TABLE items ADD CONSTRAINT items_stock_quantity_non_negative CHECK (stock_quantity >= 0)');
        DB::statement('ALTER TABLE items ADD CONSTRAINT items_low_stock_threshold_non_negative CHECK (low_stock_threshold >= 0)');
        DB::statement('CREATE UNIQUE INDEX items_sku_unique ON items (sku) WHERE sku IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS items_sku_unique');
        DB::statement('ALTER TABLE items DROP CONSTRAINT IF EXISTS items_low_stock_threshold_non_negative');
        DB::statement('ALTER TABLE items DROP CONSTRAINT IF EXISTS items_stock_quantity_non_negative');
        DB::statement('ALTER TABLE items DROP CONSTRAINT IF EXISTS items_price_non_negative');

        Schema::table('suppliers', function (Blueprint $table): void {
            $table->dropUnique(['name']);
        });

        Schema::table('units', function (Blueprint $table): void {
            $table->dropUnique(['symbol']);
            $table->dropUnique(['name']);
        });

        Schema::table('subcategories', function (Blueprint $table): void {
            $table->dropUnique(['category_id', 'name']);
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique(['name']);
        });
    }
};
