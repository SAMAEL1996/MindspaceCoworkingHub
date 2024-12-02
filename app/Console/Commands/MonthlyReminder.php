<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class MonthlyReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-reminder {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Monthly User reminder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        $monthlyUsers = \App\Models\MonthlyUser::where('is_expired', false)->get();
        $now = \Carbon\Carbon::now();

        $apikey = config('app.semaphore_key');

        foreach($monthlyUsers as $monthly) {
            $expireIn = $now->copy()->diffInDays(\Carbon\Carbon::parse($monthly->date_finish)->addDay());
            
            if($expireIn == 3 && $type == 'expiring'){
                $content = 'Your monthly pass subscription will expire in ' . $expireIn . ' day/s. Please renew your subscription to continue unlimited coworking space access. Thank you!';
                $params = [
                    'apikey' => $apikey,
                    'number' => $monthly->contact_no,
                    'message' => $content,
                ];

                try {
                    $client = new Client();
                    $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
                    $res = $client->sendAsync($request)->wait();
                } catch (\Exception $e) {
                    \Log::error($monthly->name.' send sms error on '.$now->copy()->format(config('app.date_time_carbon')) . ' with message: '. $e->getMessage());
                }

                activity()
                    ->inLog('notifications')
                    ->performedOn($monthly)
                    ->log('<b>SMS Notification</b> <br>'.$content);
            }

            if($type == 'expired' && \Carbon\Carbon::parse($monthly->date_start)->addMonth()->isToday()) {
                $monthly->card_id = null;
                $monthly->is_expired = true;
                $monthly->save();
                
                $content = 'Your monthly pass subscription has expired. Please renew your subscription to continue unlimited coworking space access. Thank you!';
                $params = [
                    'apikey' => $apikey,
                    'number' => $monthly->contact_no,
                    'message' => $content,
                ];

                try {
                    $client = new Client();
                    $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
                    $res = $client->sendAsync($request)->wait();
                } catch (\Exception $e) {
                    \Log::error($monthly->name.' send sms error on '.$now->copy()->format(config('app.date_time_carbon')) . ' with message: '. $e->getMessage());
                }

                activity()
                    ->inLog('notifications')
                    ->performedOn($monthly)
                    ->log('<b>SMS Notification</b> <br>'.$content);
            }
        }
    }
}
