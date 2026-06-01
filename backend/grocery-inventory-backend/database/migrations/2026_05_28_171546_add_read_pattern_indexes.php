<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('unit_id');
            $table->index('supplier_id');
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropIndex(['stock_quantity']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['subcategory_id']);
            $table->dropIndex(['category_id']);
        });
    }
};
