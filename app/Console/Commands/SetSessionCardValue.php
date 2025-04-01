<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetSessionCardValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-session-card-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = \App\Models\User::find(1);
        $card = $user->getMetaValue('rfid');

        session()->forget('card');

        session()->put('card',$card);

        \Cache::put('card', $card, 300);
    }
}
