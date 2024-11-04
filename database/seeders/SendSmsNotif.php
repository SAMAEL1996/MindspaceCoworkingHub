<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SendSmsNotif extends Seeder
{
    public $apiKey;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        return;
        
        $this->apiKey = config('app.semaphore_key');

        $content = "Good day! Please be informed of our adjusted schedule in observance of the upcoming holiday:\n\n- October 31 (Open until 10pm only)\n- November 1-3 (Closed)\n- November 4 (Open at 7am, Resuming normal operations)\n\nThank you!";

        $flexiUsers = \App\Models\FlexiUser::where('status', true)->get();
        foreach($flexiUsers as $flexi) {
            if($flexi->contact_no) {
                $this->sendSms($flexi->contact_no, $content);

                usleep(800 * 1000);
            }
        }
    }

    public function sendSms($contact, $content)
    {
        try {
            $client = new Client();

            $params = [
                'apikey' => $this->apiKey,
                'number' => $contact,
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
