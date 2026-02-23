<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UpdateSalesTable extends Seeder
{
    protected $toTruncate = ['sales'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        
        foreach($this->toTruncate as $table) {
            \DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $this->addSalesReport();

        Model::reguard();
    }

    public function addSalesReport() {
        $dailySales = \App\Models\DailySale::withTrashed()
                        ->where('is_flexi', false)
                        ->where('is_monthly', false)
                        ->where('is_conference', false)
                        ->get();
                        
        foreach($dailySales as $daily) {
            $timeIn = Carbon::parse($daily->time_in);

            $day = $timeIn->copy()->format('d');
            $month = $timeIn->copy()->format('F');
            $year = $timeIn->copy()->format('Y');

            // DAILY SALE
            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_daily_users += 1;
            $dailySale->total_sales += (double)$daily->amount_paid;
            $dailySale->save();

            // MONTHLY SALE
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_daily_users += 1;
            $monthlySale->total_sales += (double)$daily->amount_paid;
            $monthlySale->save();
        }
        
        $flexiSales = \App\Models\FlexiUser::where('paid', true)->get();
        foreach($flexiSales as $flexi) {
            $createdAt = Carbon::parse($flexi->created_at);

            $day = $createdAt->copy()->format('d');
            $month = $createdAt->copy()->format('F');
            $year = $createdAt->copy()->format('Y');

            // DAILY SALE
            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_flexi_users += 1;
            $dailySale->total_sales += (double)$flexi->amount;
            $dailySale->save();

            // MONTHLY SALE
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_flexi_users += 1;
            $monthlySale->total_sales += (double)$flexi->amount;
            $monthlySale->save();
        }

        $monthlySales = \App\Models\MonthlyUser::all();
        foreach($monthlySales as $monthly) {
            $createdAt = Carbon::parse($monthly->created_at);

            $day = $createdAt->copy()->format('d');
            $month = $createdAt->copy()->format('F');
            $year = $createdAt->copy()->format('Y');

            // DAILY SALE
            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_monthly_users += 1;
            $dailySale->total_sales += (double)$monthly->amount;
            $dailySale->save();

            // MONTHLY SALE
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_monthly_users += 1;
            $monthlySale->total_sales += (double)$monthly->amount;
            $monthlySale->save();
        }

        $conferenceSales = \App\Models\Conference::all();
        foreach($conferenceSales as $conference) {
            $createdAt = Carbon::parse($conference->created_at);

            $day = $createdAt->copy()->format('d');
            $month = $createdAt->copy()->format('F');
            $year = $createdAt->copy()->format('Y');

            // DAILY SALE
            $dailySale = \App\Models\Sale::where('type', 'daily')->where('day', $day)->where('month', $month)->where('year', $year)->first();
            if(!$dailySale) {
                $dailySale = \App\Models\Sale::create(['type' => 'daily', 'day' => $day, 'month' => $month, 'year' => $year]);
            }
            $dailySale->total_conference_users += 1;
            $dailySale->total_sales += (double)$conference->payment;
            $dailySale->save();

            // MONTHLY SALE
            $monthlySale = \App\Models\Sale::where('type', 'monthly')->where('month', $month)->where('year', $year)->first();
            if(!$monthlySale) {
                $monthlySale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $month, 'year' => $year]);
            }
            $monthlySale->total_conference_users += 1;
            $monthlySale->total_sales += (double)$conference->payment;
            $monthlySale->save();
        }
    }
}
