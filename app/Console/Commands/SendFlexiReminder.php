<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SendFlexiReminder extends Command
{
    public $apiKey;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mindspace:send-flexi-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send flexi user a reminder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $this->apiKey = config('app.semaphore_key');

        $flexiPass = \App\Models\FlexiUser::where('status', true)->get();
        foreach($flexiPass as $flexi) {
            $expiredAt = $flexi->expired_at_carbon;
        
            $content = "Hi {$flexi->name}! This is a reminder that your Flexi Pass with remaining time {$flexi->remaining_time}, will be expired on {$flexi->expired_at_carbon->format(config('app.date_time_format'))}. Thank you!";
            $firstNotif = $flexi->hasMeta('7-day-expiry-notification');
            $secondNotif = $flexi->hasMeta('1-day-expiry-notification');
            $thirdNotif = $flexi->hasMeta('on-day-expiry-notification');

            if (!$firstNotif && $expiredAt->copy()->subDay()->isSameDay($now->copy()->addDays(7))) {
                $res = $this->sendSms($flexi, $content);

                if($res) {
                    $flexi->addOrUpdateMeta('7-day-expiry-notification', $now->copy()->format(config('app.date_time_format')));
                }
            }

            if (!$secondNotif && $expiredAt->copy()->subDay()->isSameDay($now->copy()->addDay())) {
                $res = $this->sendSms($flexi, $content);

                if($res) {
                    $flexi->addOrUpdateMeta('1-day-expiry-notification', $now->copy()->format(config('app.date_time_format')));
                }
            }

            if (!$thirdNotif && $expiredAt->copy()->subDay()->isToday()) {
                $res = $this->sendSms($flexi, $content);

                if($res) {
                    $flexi->addOrUpdateMeta('on-day-expiry-notification', $now->copy()->format(config('app.date_time_format')));
                }
            }
        }
    }

    public function sendSms($flexi, $content)
    {
        try {
            $client = new Client();

            $params = [
                'apikey' => $this->apiKey,
                'number' => $flexi->contact_no,
                'message' => $content,
            ];
            $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
            $res = $client->sendAsync($request)->wait();

            return true;
        } catch(\Exception  $e) {
            \Log::info($e->getMessage());

            return false;
        }
    }
}
