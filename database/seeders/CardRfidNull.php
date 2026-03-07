<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardRfidNull extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = Card::whereNotNull('rfid')->get();

        foreach($cards as $card) {
            $card->update(['rfid' => null]);
        }
    }
}
