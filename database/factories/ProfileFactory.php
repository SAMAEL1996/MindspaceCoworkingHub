<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->uuid(),
            'staff_id' => fn () => Staff::withoutEvents(fn () => Staff::factory()->create())->id,
            'sss' => fake()->boolean(),
            'pagibig' => fake()->boolean(),
            'philhealth' => fake()->boolean(),
            'tin' => fake()->boolean(),
            'psa' => fake()->boolean(),
            'nbi' => fake()->boolean(),
            'brgy_clearance' => fake()->boolean(),
            'diploma' => fake()->boolean(),
            'medical' => fake()->boolean(),
            'coe' => fake()->boolean(),
            'bir' => fake()->boolean(),
            'id_picture_1' => fake()->boolean(),
            'id_picture_2' => fake()->boolean(),
            'deadline' => fake()->optional()->date(),
            'status' => fake()->boolean(),
        ];
    }
}
