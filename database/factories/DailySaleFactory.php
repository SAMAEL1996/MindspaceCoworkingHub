<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\DailySale;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailySale>
 */
class DailySaleFactory extends Factory
{
    protected $model = DailySale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timeIn = Carbon::instance(fake()->dateTimeBetween('-1 month', 'now'));
        $totalTime = fake()->numberBetween(1, 12);
        $timeOut = $timeIn->copy()->addHours($totalTime);
        $applyDiscount = fake()->boolean(20);
        $discount = $applyDiscount ? fake()->randomElement([10, 15, 20, 25]) : 0;

        if ($totalTime < 4) {
            $amountPaid = $totalTime * 75;
        } elseif ($totalTime <= 5) {
            $amountPaid = 280;
        } elseif ($totalTime <= 8) {
            $amountPaid = 380;
        } else {
            $amountPaid = 500;
        }

        if ($applyDiscount) {
            $amountPaid -= $amountPaid * ($discount / 100);
        }

        return [
            'uid' => fake()->uuid(),
            'date' => $timeIn->toDateString(),
            'card_id' => Card::factory()->daily(),
            'name' => fake()->name(),
            'description' => fake()->randomElement(['Coworking Pass', 'Hot Desk', 'Walk-in', 'Conference']),
            'time_in' => $timeIn,
            'time_in_staff_id' => Staff::factory(),
            'time_out' => $timeOut,
            'time_out_staff_id' => Staff::factory(),
            'default_amount' => fake()->boolean(),
            'total_time' => $totalTime,
            'amount_paid' => round($amountPaid, 2),
            'apply_discount' => $applyDiscount,
            'discount' => $discount,
            'is_flexi' => false,
            'is_monthly' => false,
            'is_conference' => false,
            'mode_of_payment' => fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']),
            'status' => true,
            'deleted_at' => null,
        ];
    }
}
