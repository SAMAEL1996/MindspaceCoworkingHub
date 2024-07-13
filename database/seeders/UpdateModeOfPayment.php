<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Session;

class UpdateModeOfPayment extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Session::put('cashier', false);

        foreach(\App\Models\DailySale::whereNotNull('mode_of_payment')->get() as $sale) {
            if($sale->mode_of_payment == 'cash') {
                $sale->mode_of_payment = 'Cash';
            } elseif($sale->mode_of_payment == 'gcash') {
                $sale->mode_of_payment = 'GCash';
            } elseif($sale->mode_of_payment == 'bank') {
                $sale->mode_of_payment = 'Bank Transfer';
            }

            $sale->save();
        }
    }
}
