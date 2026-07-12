<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Conference;
use App\Models\ConferenceMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConferenceMember>
 */
class ConferenceMemberFactory extends Factory
{
    protected $model = ConferenceMember::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timeIn = fake()->boolean(70)
            ? Carbon::instance(fake()->dateTimeBetween('-2 weeks', 'now'))
            : null;

        return [
            'conference_id' => Conference::factory(),
            'card_id' => Card::factory()->daily(),
            'guest' => fake()->name(),
            'status' => fake()->boolean(90),
            'time_in' => $timeIn,
            'time_out' => $timeIn ? $timeIn->copy()->addHours(fake()->numberBetween(1, 8)) : null,
        ];
    }
}
