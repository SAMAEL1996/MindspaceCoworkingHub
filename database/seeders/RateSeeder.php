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
        $data1 = [
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

        $data2 = [
            [
                'type' => 'Conference',
                'name' => 'Package 1 - 3hrs',
                'consumable' => 3,
                'validity' => null,
                'price' => 1500,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 1 - 5hrs',
                'consumable' => 5,
                'validity' => null,
                'price' => 2000,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 1 - 8hrs',
                'consumable' => 8,
                'validity' => null,
                'price' => 2500,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 1 - 24hrs',
                'consumable' => 24,
                'validity' => null,
                'price' => 3500,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 2 - 3hrs',
                'consumable' => 3,
                'validity' => null,
                'price' => 2000,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 2 - 5hrs',
                'consumable' => 5,
                'validity' => null,
                'price' => 2500,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 2 - 8hrs',
                'consumable' => 8,
                'validity' => null,
                'price' => 3000,
                'status' => true
            ],
            [
                'type' => 'Conference',
                'name' => 'Package 2 - 24hrs',
                'consumable' => 24,
                'validity' => null,
                'price' => 4500,
                'status' => true
            ],
        ];

        foreach($data2 as $item) {
            $rate = \App\Models\Rate::firstOrCreate(
                ['name' => $item['name']],
                [
                    'type' => $item['type'],
                    'name' => $item['name'],
                    'consumable' => $item['consumable'],
                    'validity' => $item['validity'],
                    'price' => $item['price'],
                    'status' => $item['status']
                ]
            );
        }

        \App\Models\Setting::upsertValue('conference-package-1-additional-person',300);
        \App\Models\Setting::upsertValue('conference-package-1-succeeding-hours',250);
        \App\Models\Setting::upsertValue('conference-package-2-succeeding-hours',300);
    }
}
