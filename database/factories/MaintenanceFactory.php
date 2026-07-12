<?php

namespace Database\Factories;

use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maintenance>
 */
class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->uuid(),
            'title' => fake()->randomElement([
                'Aircon Cleaning',
                'Network Checkup',
                'Workspace Deep Clean',
                'Generator Inspection',
            ]),
            'date' => fake()->date(),
            'status' => fake()->boolean(90),
        ];
    }
}
