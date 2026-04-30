<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateRecord extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('activity_log')
            ->whereIn('subject_type', ['App\Models\SaleReport', 'App\Models\Sale'])
            ->delete();
    }
}
