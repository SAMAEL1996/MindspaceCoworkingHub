<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = Carbon::instance(fake()->dateTimeBetween('-1 month', 'now'));
        $checkOut = fake()->boolean(80)
            ? $checkIn->copy()->addHours(fake()->numberBetween(4, 12))
            : null;
        $approveOvertime = $checkOut !== null && fake()->boolean(20);

        return [
            'staff_id' => Staff::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'generate_report' => fake()->boolean(90),
            'approve_overtime' => $approveOvertime,
            'total_overtime_hours' => $approveOvertime ? fake()->numberBetween(1, 4) : null,
            'restday_overtime' => $approveOvertime ? fake()->boolean() : false,
        ];
    }
}
