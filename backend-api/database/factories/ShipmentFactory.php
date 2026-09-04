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
        static $index = 1;
        $year = now()->year;

        return [
            "warehouse_id" => Warehouse::factory(),
            "created_by" => User::factory(),
            "shipment_number" => sprintf('SHIP-%d-%04d', $year, $index++),
            "shipped_at" => $this->faker->dateTimeThisYear(),
        ];
    }
}
