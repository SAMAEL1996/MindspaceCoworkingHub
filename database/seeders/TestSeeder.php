<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(\App\Models\MonthlyUser::all() as $monthly) {
            $monthly->contact_no = '09159473345';
            $monthly->save();
        }
    }
}
