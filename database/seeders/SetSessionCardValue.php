<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetSessionCardValue extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($card = null): void
    {
        session()->forget('card');

        session()->put('card',$card);

        \Cache::put('card', $card, 300);
    }
}
