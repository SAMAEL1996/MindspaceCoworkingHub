<?php

namespace Database\Factories;

use App\Models\ErrorLog;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ErrorLog>
 */
class ErrorLogFactory extends Factory
{
    protected $model = ErrorLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => Staff::factory(),
            'subjectable_type' => null,
            'subjectable_id' => null,
            'reason' => fake()->sentence(),
            'data' => json_encode([
                'message' => fake()->sentence(),
                'logged_at' => now()->toDateTimeString(),
            ]),
        ];
    }
}
