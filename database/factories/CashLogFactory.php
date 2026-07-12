<?php

namespace Database\Factories;

use App\Models\CashLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashLog>
 */
class CashLogFactory extends Factory
{
    protected $model = CashLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cashIn = fake()->randomFloat(2, 500, 10000);
        $isOpen = fake()->boolean(40);
        $dateCashIn = Carbon::instance(fake()->dateTimeBetween('-1 month', 'now'));
        $cashOut = $isOpen ? 0.00 : fake()->randomFloat(2, 0, $cashIn);

        return [
            'user_id' => User::factory(),
            'cash_in' => $cashIn,
            'date_cash_in' => $dateCashIn,
            'cash_out' => $cashOut,
            'date_cash_out' => $isOpen ? null : $dateCashIn->copy()->addHours(fake()->numberBetween(4, 12)),
            'total_sales' => fake()->randomFloat(2, 0, 20000),
            'status' => $isOpen,
        ];
    }
}
