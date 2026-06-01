<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'sku' => null,
            'category_id' => Category::factory(),
            'subcategory_id' => fn (array $attributes): int => Subcategory::factory()->create([
                'category_id' => $attributes['category_id'],
            ])->id,
            'unit_id' => Unit::factory(),
            'supplier_id' => Supplier::factory(),
            'price' => fake()->randomFloat(2, 0, 999),
            'stock_quantity' => fake()->numberBetween(0, 500),
            'low_stock_threshold' => 10,
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function lowStock(): self
    {
        return $this->state(fn (): array => [
            'stock_quantity' => 1,
            'low_stock_threshold' => 5,
        ]);
    }
}
