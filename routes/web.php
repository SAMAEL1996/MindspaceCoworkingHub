<?php

use App\Models\UserLocation;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('frontend.dashboard.index');
    return redirect()->to(Filament::getUrl());
});

Route::get('/test-index', function () {
    return view('frontend.dashboard.index');
});

Route::get('/test', function () {
    abort(500);
});

Route::get('/book-conference', \App\Filament\Pages\BookConference::class)->name('book-conference.index');
Route::get('/book-conference/success', \App\Filament\Pages\SuccessBooking::class)->name('book-conference.success');

Route::get('user-locations', function (Request $request) {
    // Define your expected hash
    $expectedHash = '1429-b1c5-f1d6-3dee-da79-d091-cc3e-ce96-c176-a0aa';

    // Check if the hash matches
    if (request('hash') !== $expectedHash) {
        abort(403, 'Unauthorized access.');
    }

    $filter = request('filter');
    $users = UserLocation::with('user')->latest()->get();

    return view('user-locations', compact('users'));
});

Route::get('/flexi', function() {
    $flexi = null;
    $time = [];
    $error = false;
    
    if(request()->has('user')) {
        $flexi = \App\Models\FlexiUser::where('uid', request('user'))->first();

        if($flexi) {
            $time = $flexi->getRemainingTimeArray();
        } else {
            $error = true;
        }
    }

    return view('frontend.flexi.show', compact('flexi', 'time', 'error'));
})->name('flexi.remaining-time');

Route::post('/flexi', function(Request $request) {
    $request->validate([
        'contact' => ['required', 'regex:/^09\d{9}$/', 'size:11'],
    ]);

    $flexi = \App\Models\FlexiUser::where('contact_no', request('contact'))->where('status', true)->latest()->first();

    return redirect()->route('flexi.remaining-time', ['user' => $flexi->uid]);
})->name('flexi.remaining-time');

Route::get('/external/rfid-scan', function(Request $request) {

    \Cache::put('card', 'hey', 300);
    abort(403);

    $user = \App\Models\User::find(1);
    $user->addOrUpdateMeta('rfid', $uidResult);
});
Route::middleware(['web'])->post('/external/rfid-scan', function(Request $request) {
    $uidResult = $request->input('UIDresult');
    \App\Models\Setting::upsertValue('card', $uidResult);

    $card = \App\Models\Card::where('rfid', $uidResult)->first();
    if($card) {
        switch($card->type) {
            case 'Staff':
                $staff = \App\Models\Staff::where('card_id', $card->id)->first();
                $user = $staff->user;
                $attendance = $staff->attendances()->latest()->first();
                if($attendance->check_out) {
                    // create new attendance
                    $newAttendance = \App\Models\Attendance::create([
                        'staff_id' => $staff->id,
                        'check_in' => \Carbon\Carbon::now()
                    ]);
                } else {
                    // logout
                    $attendance->update([
                        'check_out' => \Carbon\Carbon::now()
                    ]);
                }
                break;
        }
    }

    return response()->json(['message' => 'RFID received successfully']);
});