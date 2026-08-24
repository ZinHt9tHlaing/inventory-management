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

        $warehouses = Warehouse::factory(5)->create();

        Supplier::factory(5)
            ->has(
                Product::factory(3)->afterCreating(function (Product $product) use ($warehouses) {
                    // one to three warehouses are randomly selected from the five previously created warehouses.
                    $randomWarehouses = $warehouses->random(fake()->numberBetween(1, 3));

                    foreach ($randomWarehouses as $warehouse) {
                        Inventory::factory()->create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse->id,
                        ]);
                    }
                })
            )
            ->create();
    }
}
