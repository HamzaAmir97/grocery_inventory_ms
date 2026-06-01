<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Piece', 'symbol' => 'pcs'],
            ['name' => 'Pack', 'symbol' => 'pack'],
        ];

        foreach ($units as $unit) {
            Unit::query()->updateOrCreate(
                ['name' => $unit['name']],
                $unit + ['is_active' => true]
            );
        }
    }
}
