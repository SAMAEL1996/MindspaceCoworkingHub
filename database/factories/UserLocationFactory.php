<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLocation>
 */
class UserLocationFactory extends Factory
{
    protected $model = UserLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'lat' => (string) fake()->latitude(),
            'long' => (string) fake()->longitude(),
        ];
    }
}
