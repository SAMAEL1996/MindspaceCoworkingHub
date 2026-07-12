<?php

namespace Database\Factories;

use App\Models\LoyaltyCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoyaltyCard>
 */
class LoyaltyCardFactory extends Factory
{
    protected $model = LoyaltyCard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slot = fake()->numberBetween(0, 11);

        return [
            'uid' => fake()->uuid(),
            'type' => fake()->randomElement(['student', 'professional']),
            'card_no' => strtoupper(fake()->unique()->bothify('LC-########')),
            'slot' => $slot,
            'is_compleated' => $slot >= 11,
            'status' => true,
        ];
    }
}
