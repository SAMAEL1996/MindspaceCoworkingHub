<?php

namespace Database\Factories;

use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rate>
 */
class RateFactory extends Factory
{
    protected $model = Rate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $preset = fake()->randomElement([
            ['type' => 'Daily', 'name' => 'Hourly Pass', 'consumable' => 1, 'validity' => null, 'price' => 75],
            ['type' => 'Daily', 'name' => '5-Hourly Pass', 'consumable' => 5, 'validity' => null, 'price' => 280],
            ['type' => 'Daily', 'name' => '8-Hourly Pass', 'consumable' => 8, 'validity' => null, 'price' => 380],
            ['type' => 'Daily', 'name' => 'Whole Day Pass', 'consumable' => 24, 'validity' => null, 'price' => 500],
            ['type' => 'Flexi', 'name' => 'Flexi Pass 1500', 'consumable' => 50, 'validity' => 60, 'price' => 1500],
            ['type' => 'Flexi', 'name' => 'Flexi Pass 2500', 'consumable' => 100, 'validity' => 90, 'price' => 2500],
            ['type' => 'Monthly', 'name' => 'Monthly Pass', 'consumable' => null, 'validity' => 30, 'price' => 5500],
            ['type' => 'Conference', 'name' => 'Package 1 - 3hrs', 'consumable' => 3, 'validity' => null, 'price' => 1500],
        ]);

        return array_merge([
            'uid' => fake()->uuid(),
            'status' => true,
        ], $preset);
    }

    public function daily(?int $consumable = null): static
    {
        $consumable ??= fake()->randomElement([1, 5, 8, 24]);
        $rates = [
            1 => ['Hourly Pass', 75],
            5 => ['5-Hourly Pass', 280],
            8 => ['8-Hourly Pass', 380],
            24 => ['Whole Day Pass', 500],
        ];

        return $this->state(fn (array $attributes) => [
            'uid' => fake()->uuid(),
            'type' => 'Daily',
            'name' => $rates[$consumable][0],
            'consumable' => $consumable,
            'validity' => null,
            'price' => $rates[$consumable][1],
            'status' => true,
        ]);
    }

    public function flexi(?int $consumable = null): static
    {
        $consumable ??= fake()->randomElement([50, 100]);
        $presets = [
            50 => ['Flexi Pass 1500', 60, 1500],
            100 => ['Flexi Pass 2500', 90, 2500],
        ];

        return $this->state(fn (array $attributes) => [
            'uid' => fake()->uuid(),
            'type' => 'Flexi',
            'name' => $presets[$consumable][0],
            'consumable' => $consumable,
            'validity' => $presets[$consumable][1],
            'price' => $presets[$consumable][2],
            'status' => true,
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'uid' => fake()->uuid(),
            'type' => 'Monthly',
            'name' => 'Monthly Pass',
            'consumable' => null,
            'validity' => 30,
            'price' => 5500,
            'status' => true,
        ]);
    }

    public function conference(int $packageId = 1, ?int $consumable = null): static
    {
        $consumable ??= fake()->randomElement([3, 5, 8, 24]);
        $prices = [
            1 => [3 => 1500, 5 => 2000, 8 => 2500, 24 => 3500],
            2 => [3 => 2000, 5 => 2500, 8 => 3000, 24 => 4500],
        ];

        return $this->state(fn (array $attributes) => [
            'uid' => fake()->uuid(),
            'type' => 'Conference',
            'name' => sprintf('Package %d - %dhrs', $packageId, $consumable),
            'consumable' => $consumable,
            'validity' => null,
            'price' => $prices[$packageId][$consumable],
            'status' => true,
        ]);
    }
}
