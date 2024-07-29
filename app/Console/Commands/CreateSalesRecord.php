<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateSalesRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sales-record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a daily and monthly sales record.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();

        $month = $now->copy()->format('F');
        $year = $now->copy()->format('Y');
        $day = $now->copy()->day;

        $monthly = \App\Models\Sale::firstOrCreate(['type' => 'monthly', 'month' => $month, 'year' => $year]);
        $daily = \App\Models\Sale::firstOrCreate(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);

        Artisan::call('db:seed --class=UpdateSalesTable');
    }
}
