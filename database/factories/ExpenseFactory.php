<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 20);
        $unitPrice = fake()->randomFloat(2, 50, 2000);

        return [
            'uid' => fake()->uuid(),
            'item' => fake()->randomElement([
                'Office Supplies',
                'Cleaning Materials',
                'Coffee Refill',
                'Printer Ink',
                'Internet Bill',
            ]),
            'quantity' => $quantity,
            'amount' => round($quantity * $unitPrice, 2),
        ];
    }
}
