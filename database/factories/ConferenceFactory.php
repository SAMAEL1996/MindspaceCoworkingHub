<?php

namespace Database\Factories;

use App\Models\Conference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conference>
 */
class ConferenceFactory extends Factory
{
    protected $model = Conference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $packageId = fake()->randomElement([1, 2]);
        $duration = fake()->randomElement([3, 5, 8, 12, 24]);
        $startAt = Carbon::instance(fake()->dateTimeBetween('+1 day', '+2 months'));
        $amount = $packageId === 1
            ? fake()->randomFloat(2, 1500, 3500)
            : fake()->randomFloat(2, 2000, 4500);
        $hasReservationFee = fake()->boolean(35);
        $isPaid = fake()->boolean(50);
        $payment = $isPaid ? $amount : fake()->randomFloat(2, 0, $amount);
        $modeOfPayment = $payment > 0 ? fake()->randomElement(['Cash', 'GCash', 'Bank Transfer']) : null;

        return [
            'uid' => fake()->uuid(),
            'package_id' => $packageId,
            'book_by' => User::factory(),
            'start_at' => $startAt,
            'duration' => $duration,
            'event' => fake()->randomElement(['Board Meeting', 'Workshop', 'Client Presentation', 'Team Planning']),
            'members' => $packageId === 1
                ? fake()->numberBetween(2, 8)
                : fake()->numberBetween(10, 15),
            'host' => fake()->name(),
            'email' => fake()->safeEmail(),
            'contact_no' => fake()->numerify('09#########'),
            'status' => fake()->randomElement(['pending', 'approve', 'finished', 'cancelled']),
            'amount' => $amount,
            'has_reservation_fee' => $hasReservationFee,
            'mop_reservation_fee' => $hasReservationFee
                ? fake()->randomElement(['Cash', 'GCash', 'Bank Transfer'])
                : null,
            'payment' => $payment,
            'is_paid' => $isPaid,
            'mode_of_payment' => $modeOfPayment,
        ];
    }
}
