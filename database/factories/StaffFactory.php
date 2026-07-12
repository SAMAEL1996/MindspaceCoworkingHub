<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Staff>
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->uuid(),
            'user_id' => User::factory()->state([
                'is_staff' => true,
                'status' => true,
            ]),
            'card_id' => Card::factory()->staff(),
            'personal_email' => fake()->safeEmail(),
            'is_active' => true,
            'emergency_contact_person' => fake()->name(),
            'emergency_relationship' => fake()->randomElement(['Parent', 'Sibling', 'Partner', 'Friend']),
            'emergency_contact_no' => fake()->numerify('09#########'),
        ];
    }
}
