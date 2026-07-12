<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'log_name' => fake()->randomElement(['default', 'notifications']),
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'causer_type' => User::class,
            'causer_id' => User::factory(),
            'event' => fake()->randomElement(['created', 'updated', 'deleted']),
            'properties' => [
                'ip_address' => fake()->ipv4(),
                'notes' => fake()->sentence(),
            ],
            'batch_uuid' => fake()->optional()->uuid(),
        ];
    }
}
