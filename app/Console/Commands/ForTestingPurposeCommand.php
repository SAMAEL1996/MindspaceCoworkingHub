<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ForTestingPurposeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:for-testing-purpose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For testing purpose command only';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();

        \Log::info('Logging test command at: '.$now->copy()->format('M d, Y H:i:s'));
    }
}
