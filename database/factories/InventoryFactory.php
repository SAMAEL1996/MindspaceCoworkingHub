<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(0, 100);

        if ($quantity === 0) {
            $status = 'Out of Stock';
        } elseif ($quantity <= 5) {
            $status = 'Running Out';
        } else {
            $status = 'In Stock';
        }

        return [
            'uid' => fake()->uuid(),
            'user_id' => User::factory(),
            'item' => fake()->randomElement([
                'Printer Paper',
                'Coffee Beans',
                'Water Bottle',
                'Sticky Notes',
                'Ink Cartridge',
            ]),
            'quantity' => $quantity,
            'unit' => fake()->randomElement(['pcs', 'packs', 'boxes', 'bottles']),
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => $status,
            'is_active' => fake()->boolean(90),
            'deleted_at' => null,
        ];
    }
}
