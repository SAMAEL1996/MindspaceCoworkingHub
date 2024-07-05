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
            $startAt = $flexi->start_at_carbon;
            $endAt = $flexi->end_at_carbon;

            $interval = $startAt->diff($endAt);
            $hours = $interval->h;

            $content = "Hi {$flexi->name}! This is a reminder that you only have {$flexi->remaining_time} on your Flexi Pass. Thank you!";
            $firstNotif = $flexi->hasMeta('10-hour-notification');
            $secondNotif = $flexi->hasMeta('3-hour-notification');

            if(!$firstNotif) {
                $res = $this->sendSms($flexi, $content);

                if($res) {
                    $flexi->addOrUpdateMeta('10-hour-notification', $now->copy()->format(config('app.date_time_format')));
                }
            }

            if($firstNotif && !$secondNotif) {
                $res = $this->sendSms($flexi, $content);

                if($res) {
                    $flexi->addOrUpdateMeta('3-hour-notification', $now->copy()->format(config('app.date_time_format')));
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
