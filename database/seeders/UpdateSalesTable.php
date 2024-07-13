<?php

namespace Database\Seeders;

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

        $this->addMonthlySalesReport();
        $this->addDailySalesReport();

        Model::reguard();
    }

    public function addDailySalesReport() {
        $dailySales = \App\Models\DailySale::selectRaw('year(time_in) year, monthname(time_in) month, day(time_in) day, count(*) data, sum(amount_paid) total')
                        ->where('is_flexi', false)
                        ->where('is_monthly', false)
                        ->groupBy('year', 'month', 'day')
                        ->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->orderBy('day', 'desc')
                        ->get();
                        
        foreach($dailySales as $daily) {
            $sale = \App\Models\Sale::where('type', 'daily')->where('day', $daily->day)->where('month', $daily->month)->where('year', $daily->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'daily', 'day' => $daily->day, 'month' => $daily->month, 'year' => $daily->year]);
            }

            $sale->total_daily_users = $daily->data;
            $sale->total_sales += (double)$daily->total;
            $sale->save();
        }
        
        $flexiSales = \App\Models\FlexiUser::selectRaw('year(start_at) year, monthname(start_at) month, day(start_at) day, count(*) data, sum(amount) total')
                                            ->where('paid', true)
                                            ->groupBy('year', 'month', 'day')
                                            ->orderBy('year', 'desc')
                                            ->orderBy('month', 'desc')
                                            ->orderBy('day', 'desc')
                                            ->get();
        foreach($flexiSales as $flexi) {
            $sale = \App\Models\Sale::where('type', 'daily')->where('day', $flexi->day)->where('month', $flexi->month)->where('year', $flexi->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'daily', 'day' => $flexi->day, 'month' => $flexi->month, 'year' => $flexi->year]);
            }

            $sale->total_flexi_users = $flexi->data;
            $sale->total_sales += (double)$flexi->total;
            $sale->save();
        }

        $monthlySales = \App\Models\MonthlyUser::selectRaw('year(date_start) year, monthname(date_start) month, day(created_at) day, count(*) data, sum(amount) total')
                                            ->groupBy('year', 'month', 'day')
                                            ->orderBy('year', 'desc')
                                            ->orderBy('month', 'desc')
                                            ->orderBy('day', 'desc')
                                            ->get();
        foreach($monthlySales as $monthly) {
            $sale = \App\Models\Sale::where('type', 'daily')->where('day', $monthly->day)->where('month', $monthly->month)->where('year', $monthly->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'daily', 'day' => $monthly->day, 'month' => $monthly->month, 'year' => $monthly->year]);
            }

            $sale->total_monthly_users = $monthly->data;
            $sale->total_sales += (double)$monthly->total;
            $sale->save();
        }

        $conferenceSales = \App\Models\Conference::selectRaw('year(updated_at) year, monthname(updated_at) month, day(updated_at) day, count(*) data, sum(payment) total')
                                            ->where('status', 'finished')
                                            ->groupBy('year', 'month', 'day')
                                            ->orderBy('year', 'desc')
                                            ->orderBy('month', 'desc')
                                            ->orderBy('day', 'desc')
                                            ->get();
        foreach($conferenceSales as $conference) {
            $sale = \App\Models\Sale::where('type', 'daily')->where('day', $conference->day)->where('month', $conference->month)->where('year', $conference->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'daily', 'day' => $conference->day, 'month' => $conference->month, 'year' => $conference->year]);
            }

            $sale->total_conference_users = $conference->data;
            $sale->total_sales += (double)$conference->total;
            $sale->save();
        }
    }

    public function addMonthlySalesReport() {
        $dailyPass = \App\Models\DailySale::selectRaw('year(time_in) year, monthname(time_in) month, count(*) data, sum(amount_paid) total')
                                            ->where('is_flexi', false)
                                            ->where('is_monthly', false)
                                            ->groupBy('year', 'month')
                                            ->orderBy('year', 'desc')
                                            ->get();

        foreach($dailyPass as $data) {
            $sale = \App\Models\Sale::where('type', 'monthly')->where('month', $data->month)->where('year', $data->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $data->month, 'year' => $data->year]);
            }

            $sale->total_daily_users = $data->data;
            $sale->total_sales += (double)$data->total;
            $sale->save();
        }

        $flexiPass = \App\Models\FlexiUser::selectRaw('year(start_at) year, monthname(start_at) month, count(*) data, sum(amount) total')
                                            ->where('paid', true)
                                            ->groupBy('year', 'month')
                                            ->orderBy('year', 'desc')
                                            ->get();
        foreach($flexiPass as $data) {
            $sale = \App\Models\Sale::where('type', 'monthly')->where('month', $data->month)->where('year', $data->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $data->month, 'year' => $data->year]);
            }

            $sale->total_flexi_users = $data->data;
            $sale->total_sales += (double)$data->total;
            $sale->save();
        }

        $monthlyPass = \App\Models\MonthlyUser::selectRaw('year(date_start) year, monthname(date_start) month, count(*) data, sum(amount) total')
                                            ->groupBy('year', 'month')
                                            ->orderBy('year', 'desc')
                                            ->get();
        foreach($monthlyPass as $data) {
            $sale = \App\Models\Sale::where('type', 'monthly')->where('month', $data->month)->where('year', $data->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $data->month, 'year' => $data->year]);
            }

            $sale->total_monthly_users = $data->data;
            $sale->total_sales += (double)$data->total;
            $sale->save();
        }

        $conferencePass = \App\Models\Conference::selectRaw('year(updated_at) year, monthname(updated_at) month, count(*) data, sum(payment) total')
                                            ->where('status', 'finished')
                                            ->groupBy('year', 'month')
                                            ->orderBy('year', 'desc')
                                            ->get();
        foreach($conferencePass as $data) {
            $sale = \App\Models\Sale::where('type', 'monthly')->where('month', $data->month)->where('year', $data->year)->first();
            if(!$sale) {
                $sale = \App\Models\Sale::create(['type' => 'monthly', 'month' => $data->month, 'year' => $data->year]);
            }

            $sale->total_conference_users = $data->data;
            $sale->total_sales += (double)$data->total;
            $sale->save();
        }
    }
}
