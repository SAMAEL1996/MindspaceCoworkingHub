<?php

namespace Database\Factories;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::instance(fake()->dateTimeBetween('-1 year', 'now'));
        $type = fake()->randomElement(['daily', 'monthly']);

        return [
            'type' => $type,
            'day' => $type === 'daily' ? $date->format('d') : null,
            'month' => $date->format('F'),
            'year' => (int) $date->format('Y'),
            'total_daily_users' => fake()->numberBetween(0, 200),
            'total_flexi_users' => fake()->numberBetween(0, 100),
            'total_monthly_users' => fake()->numberBetween(0, 100),
            'total_conference_users' => fake()->numberBetween(0, 50),
            'total_sales' => fake()->randomFloat(2, 0, 200000),
        ];
    }
}
