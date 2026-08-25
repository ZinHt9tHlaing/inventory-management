<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Production;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Production>
 */
class ProductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "product_id" => Product::factory(),
            "warehouse_id" => Warehouse::factory(),
            "created_by" => User::factory(),
            "quantity_produced" => $this->faker->randomNumber(2),
        ];
    }
}
