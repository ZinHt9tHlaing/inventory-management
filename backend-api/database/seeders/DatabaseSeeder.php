<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        Supplier::factory(5)
            ->has(Product::factory(3))
            ->create();

        $warehouses = Warehouse::factory(5)->create();

        $products = Product::all();

        foreach ($products as $product) {
            $randomWarehouses = $warehouses->random(rand(1, 3));

            foreach ($randomWarehouses as $warehouse) {
                Inventory::factory()->create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ]);
            }
        }
    }
}
