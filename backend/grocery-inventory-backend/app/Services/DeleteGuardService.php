<?php

namespace App\Services;

use App\Exceptions\DeleteRestrictedException;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;

class DeleteGuardService
{
    public function guardCategory(Category $category): void
    {
        if ($category->subcategories()->exists()) {
            throw new DeleteRestrictedException('This category still has subcategories.');
        }

        if ($category->items()->withTrashed()->exists()) {
            throw new DeleteRestrictedException('This category still has items.');
        }
    }

    public function guardSubcategory(Subcategory $subcategory): void
    {
        if ($subcategory->items()->withTrashed()->exists()) {
            throw new DeleteRestrictedException('This subcategory still has items.');
        }
    }

    public function guardUnit(Unit $unit): void
    {
        if ($unit->items()->withTrashed()->exists()) {
            throw new DeleteRestrictedException('This unit still has items.');
        }
    }

    public function guardSupplier(Supplier $supplier): void
    {
        if ($supplier->items()->withTrashed()->exists()) {
            throw new DeleteRestrictedException('This supplier still has items.');
        }
    }
}
