<?php

namespace Database\Factories;

use App\Models\DailySale;
use App\Models\SaleReport;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleReport>
 */
class SaleReportFactory extends Factory
{
    protected $model = SaleReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalSales = fake()->randomFloat(2, 250, 10000);
        $staffSales = fake()->randomFloat(2, 0, $totalSales);

        return [
            'uid' => fake()->uuid(),
            'staff_id' => Staff::factory(),
            'daily_sale_id' => DailySale::factory(),
            'staff_customer' => fake()->boolean(25),
            'date' => fake()->date(),
            'staff_sales' => $staffSales,
            'total_sales' => $totalSales,
            'filename' => fake()->slug() . '.xlsx',
            'status' => true,
        ];
    }
}
