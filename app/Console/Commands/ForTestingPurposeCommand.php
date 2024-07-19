<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

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
        $user = \App\Models\User::find(1);
        $user->addOrUpdateMeta('latest-test-run', $now->format(config('app.date_time_format')));

        if(!$user->hasMeta('message-sent')) {
            try {
                $apikey = config('app.semaphore_key');

                $params = [
                    'apikey' => $apikey,
                    'number' => '09159473345',
                    'message' => 'Sending Message Successful',
                ];

                $client = new Client();
                $request = new Request('POST', "https://api.semaphore.co/api/v4/messages?" . http_build_query($params));
                $res = $client->sendAsync($request)->wait();

                $user->addOrUpdateMeta('message-sent', $now->format(config('app.date_time_format')));
            } catch (\Exception $e) {
                \Log::error($monthly->name.' send sms error on '.$now->copy()->format(config('app.date_time_carbon')) . ' with message: '. $e->getMessage());
            }
        }
    }
}
