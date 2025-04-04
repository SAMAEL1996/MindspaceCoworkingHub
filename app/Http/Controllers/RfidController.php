<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\DailySale;
use Carbon\Carbon;

class RfidController extends Controller
{
    public function fetchRfid(Request $request) {
        $uidResult = $request->input('UIDresult');
        Setting::upsertValue('card', $uidResult);

        $card = Card::where('rfid', $uidResult)->first();
        if($card) {
            switch($card->type) {
                case 'Staff':
                    $staff = Staff::where('card_id', $card->id)->first();
                    $user = $staff->user;
                    $attendance = $staff->attendances()->latest()->first();
                    if($attendance->check_out) {
                        // create new attendance
                        $newAttendance = Attendance::create([
                            'staff_id' => $staff->id,
                            'check_in' => Carbon::now()
                        ]);
                    } else {
                        // logout
                        $attendance->update([
                            'check_out' => Carbon::now()
                        ]);
                    }
                    break;

                case 'Daily':
                    $daily = DailySale::where('card_id', $card->id)->where('status', true)->latest()->first();
                    if($daily) {
                        // check-out

                    } else {
                        // check-in

                    }
                    break;

                case 'Monthly':
                    break;

                case 'Conference':
                    break;
            }
        }

        return response()->json(['message' => 'RFID received successfully']);
    }
}
