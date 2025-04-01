<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'type' => 'Daily',
                'name' => 'Hourly Pass',
                'consumable' => 1,
                'validity' => null,
                'price' => 75,
                'status' => true
            ],
            [
                'type' => 'Daily',
                'name' => '5-Hourly Pass',
                'consumable' => 5,
                'validity' => null,
                'price' => 280,
                'status' => true
            ],
            [
                'type' => 'Daily',
                'name' => '8-Hourly Pass',
                'consumable' => 8,
                'validity' => null,
                'price' => 380,
                'status' => true
            ],
            [
                'type' => 'Daily',
                'name' => 'Whole Day Pass',
                'consumable' => 24,
                'validity' => null,
                'price' => 500,
                'status' => true
            ],
            [
                'type' => 'Flexi',
                'name' => 'Flexi Pass 1500',
                'consumable' => 50,
                'validity' => 60,
                'price' => 1500,
                'status' => true
            ],
            [
                'type' => 'Flexi',
                'name' => 'Flexi Pass 2500',
                'consumable' => 100,
                'validity' => 90,
                'price' => 2500,
                'status' => true
            ],
            [
                'type' => 'Monthly',
                'name' => 'Monthly Pass',
                'consumable' => null,
                'validity' => 30,
                'price' => 5500,
                'status' => true
            ]
        ];

        foreach($data as $item) {
            $rate = \App\Models\Rate::create([
                'type' => $item['type'],
                'name' => $item['name'],
                'consumable' => $item['consumable'],
                'validity' => $item['validity'],
                'price' => $item['price'],
                'status' => $item['status']
            ]);
        }
    }
}
