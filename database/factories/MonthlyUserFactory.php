<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\MonthlyUser;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyUser>
 */
class MonthlyUserFactory extends Factory
{
    protected $model = MonthlyUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateStart = Carbon::instance(fake()->dateTimeBetween('-2 weeks', 'now'))->startOfDay();
        $dateFinish = $dateStart->copy()->addDays(30);

        return [
            'uid' => fake()->uuid(),
            'rate_id' => Rate::factory()->monthly(),
            'card_id' => Card::factory()->monthly(),
            'name' => fake()->name(),
            'contact_no' => fake()->numerify('09#########'),
            'facebook' => fake()->optional()->userName(),
            'social_media' => fake()->optional()->url(),
            'date_start' => $dateStart->toDateString(),
            'date_finish' => $dateFinish->toDateString(),
            'is_active' => true,
            'is_expired' => false,
            'paid' => true,
            'amount' => 5500,
            'mode_of_payment' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
        ];
    }
}
