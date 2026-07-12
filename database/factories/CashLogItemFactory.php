<?php

namespace Database\Factories;

use App\Models\CashLog;
use App\Models\CashLogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashLogItem>
 */
class CashLogItemFactory extends Factory
{
    protected $model = CashLogItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isCashIn = fake()->boolean();
        $amount = fake()->randomFloat(2, 50, 2000);

        return [
            'cash_log_id' => CashLog::factory(),
            'in' => $isCashIn ? $amount : 0.00,
            'out' => $isCashIn ? 0.00 : $amount,
            'description' => fake()->sentence(),
        ];
    }
}
