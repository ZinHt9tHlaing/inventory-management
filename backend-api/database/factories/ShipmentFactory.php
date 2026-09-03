<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "warehouse_id" => Warehouse::factory(),
            "created_by" => User::factory(),
            "shipment_number" => $this->faker->unique()->word(),
            "shipped_at" => $this->faker->dateTimeThisYear(),
        ];
    }
}
