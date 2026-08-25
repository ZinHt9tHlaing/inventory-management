<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Production;
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

        $products = collect();

        Supplier::factory(5)
            ->has(
                Product::factory(3)->afterCreating(function (Product $product) use ($warehouses, &$products) {
                    // one to three warehouses are randomly selected from the five previously created warehouses.
                    $randomWarehouses = $warehouses->random(fake()->numberBetween(1, 3));

                    foreach ($randomWarehouses as $warehouse) {
                        Inventory::factory()->create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse->id,
                        ]);
                    }

                    $products->push($product);
                })
            )
            ->create();

        // Seed production records using existing products, warehouses, and users.
        Production::factory(5)->make()->each(function (Production $production) use ($products, $warehouses) {
            $production->product_id   = $products->random()->id;
            $production->warehouse_id = $warehouses->random()->id;
            $production->created_by   = User::all()->random()->id;
            $production->save();
        });
    }
}
