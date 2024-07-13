<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffCodes = ['MS-S-001', 'MS-S-002', 'MS-S-003', 'MS-S-004', 'MS-S-005', 'MS-S-006', 'MS-S-007'];
        foreach($staffCodes as $code) {
            \App\Models\Card::create([
                'code' => $code,
                'type' => 'Staff'
            ]);
        }
        $dailyCodes = [
            'MS-D-001', 'MS-D-002', 'MS-D-003', 'MS-D-004', 'MS-D-005', 'MS-D-006', 'MS-D-007', 'MS-D-008', 'MS-D-009', 'MS-D-010',
            'MS-D-011', 'MS-D-012', 'MS-D-013', 'MS-D-014', 'MS-D-015', 'MS-D-016', 'MS-D-017', 'MS-D-018', 'MS-D-019', 'MS-D-020'
        ];
        foreach($dailyCodes as $code) {
            \App\Models\Card::create([
                'code' => $code,
                'type' => 'Daily'
            ]);
        }

        $monthlyCodes = [
            'MS-M-001', 'MS-M-002', 'MS-M-003', 'MS-M-004', 'MS-M-005', 'MS-M-006', 'MS-M-007', 'MS-M-008', 'MS-M-009', 'MS-M-010',
        ];
        foreach($monthlyCodes as $code) {
            \App\Models\Card::create([
                'code' => $code,
                'type' => 'Monthly'
            ]);
        }
    }
}
