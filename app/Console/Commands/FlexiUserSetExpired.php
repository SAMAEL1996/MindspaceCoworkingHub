<?php

namespace App\Console\Commands;

use App\Models\FlexiUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FlexiUserSetExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:flexi-set-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Flexi User expired at';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $flexiUsers = FlexiUser::whereNotNull('expired_at')->where('status', true)->get();

        foreach($flexiUsers as $flexi) {
            if(Carbon::parse($flexi->expired_at)->isSameDay(now())) {
                $flexi->status = false;
                $flexi->save();
            }
        }
    }
}
