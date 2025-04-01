<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetSessionCardValue extends Seeder
{
    protected $card;

    public function __construct($card)
    {
        $this->card = $card;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        session()->forget('card');

        session()->put('card',$this->card);

        \Cache::put('card', $this->card, 300);
    }
}
