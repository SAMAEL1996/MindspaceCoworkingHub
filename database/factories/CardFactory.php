<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('MS-???-###')),
            'rfid' => fake()->boolean(70)
                ? fake()->unique()->numerify('##########')
                : null,
            'type' => fake()->randomElement(['Staff', 'Daily', 'Monthly']),
            'status' => fake()->randomElement(['Active', 'Inactive']),
            'deleted_at' => null,
        ];
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => strtoupper(fake()->unique()->bothify('MS-S-###')),
            'type' => 'Staff',
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => strtoupper(fake()->unique()->bothify('MS-D-###')),
            'type' => 'Daily',
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => strtoupper(fake()->unique()->bothify('MS-M-###')),
            'type' => 'Monthly',
        ]);
    }
}
