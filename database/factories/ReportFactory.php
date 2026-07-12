<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Report;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalSales = fake()->randomFloat(2, 500, 15000);
        $staffSales = fake()->randomFloat(2, 0, $totalSales);

        return [
            'uid' => fake()->uuid(),
            'staff_id' => Staff::factory(),
            'attendance_id' => Attendance::factory(),
            'date' => fake()->date(),
            'staff_sales' => $staffSales,
            'total_sales' => $totalSales,
            'filename' => fake()->slug() . '.xlsx',
            'status' => true,
        ];
    }
}
