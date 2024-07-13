<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddCardID extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            'MS-C-001',
            'MS-C-002',
            'MS-C-003',
            'MS-C-004',
            'MS-C-005',
            'MS-C-006',
            'MS-C-007',
            'MS-C-008',
            'MS-C-009',
            'MS-C-010',
            'MS-C-011',
            'MS-C-012',
            'MS-C-013',
            'MS-C-014',
            'MS-C-015',
            'MS-C-016',
            'MS-C-017',
            'MS-C-018'
        ];

        foreach($codes as $code) {
            if(!\App\Models\Card::where('code', $code)->exists()) {
                \App\Models\Card::create([
                    'code' => $code,
                    'type' => 'Conference'
                ]);
            }
        }

        $mCodes = [
            'MS-M-011',
            'MS-M-012',
            'MS-M-013',
            'MS-M-014',
            'MS-M-015',
        ];

        foreach($mCodes as $mCode) {
            if(!\App\Models\Card::where('code', $mCode)->exists()) {
                \App\Models\Card::create([
                    'code' => $mCode,
                    'type' => 'Monthly'
                ]);
            }
        }
    }
}
