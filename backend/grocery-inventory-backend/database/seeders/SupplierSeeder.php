<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fresh Farm Supplies',
                'contact_person' => 'Aisha Patel',
                'phone' => '+1-555-0101',
                'email' => 'orders@freshfarm.example',
                'address' => '12 Orchard Way, Riverside, CA',
            ],
            [
                'name' => 'Daily Dairy Co',
                'contact_person' => 'Marcus Lee',
                'phone' => '+1-555-0102',
                'email' => 'hello@dailydairy.example',
                'address' => '48 Creamery Rd, Lakeview, MN',
            ],
            [
                'name' => 'City Grocery Wholesale',
                'contact_person' => 'Priya Sharma',
                'phone' => '+1-555-0103',
                'email' => 'sales@citygrocery.example',
                'address' => '300 Market St, Springfield, IL',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::query()->updateOrCreate(
                ['name' => $supplier['name']],
                $supplier + ['is_active' => true]
            );
        }
    }
}
