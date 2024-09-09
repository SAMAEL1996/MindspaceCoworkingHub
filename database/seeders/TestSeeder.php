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
        // foreach(\App\Models\MonthlyUser::all() as $monthly) {
        //     $monthly->contact_no = '09159473345';
        //     $monthly->save();
        // }

        foreach(\App\Models\FlexiUser::all() as $flexi) {
            if(!$flexi->remaining) {
                $flexi->remaining = $flexi->start_at_carbon->diffInMinutes($flexi->end_at_carbon);
                $flexi->save();
            }
        }
    }
}
