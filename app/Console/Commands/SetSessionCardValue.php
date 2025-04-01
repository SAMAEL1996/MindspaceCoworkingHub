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
    protected $signature = 'app:set-session-card-value {card}';

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
        $card = $this->argument('card');
        
        session()->forget('card');

        session()->put('card',$card);

        \Cache::put('card', $card, 300);
    }
}
