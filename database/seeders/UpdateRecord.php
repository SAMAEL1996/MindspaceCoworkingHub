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
        $sales = \App\Models\DailySale::all();
        foreach($sales as $sale) {
            $sale->computeAmount();
        }
    }
}
