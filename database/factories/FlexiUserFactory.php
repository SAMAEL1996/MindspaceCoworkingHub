<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\FlexiUser;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlexiUser>
 */
class FlexiUserFactory extends Factory
{
    protected $model = FlexiUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $consumableHours = fake()->randomElement([50, 100]);
        $startAt = Carbon::instance(fake()->dateTimeBetween('-2 weeks', 'now'));
        $endAt = $startAt->copy()->addHours($consumableHours);
        $expiredAt = $startAt->copy()->addDays($consumableHours === 50 ? 60 : 90);

        return [
            'uid' => fake()->uuid(),
            'rate_id' => Rate::factory()->flexi($consumableHours),
            'card_id' => Card::factory()->daily(),
            'name' => fake()->name(),
            'contact_no' => fake()->numerify('09#########'),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'expired_at' => $expiredAt,
            'is_active' => true,
            'status' => true,
            'paid' => true,
            'amount' => $consumableHours === 50 ? 1500 : 2500,
            'remaining' => $startAt->diffInMinutes($endAt),
            'mode_of_payment' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
        ];
    }
}
