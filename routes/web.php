<?php

use App\Models\Card;
use App\Models\CashLog;
use App\Models\DailySale;
use Illuminate\Support\Facades\Cache;
use App\Models\UserLocation;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;

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
Route::post('/save-location', function (Request $request) {
    if (!auth()->check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    UserLocation::create([
        'user_id' => auth()->id(),
        'lat' => $request->lat,
        'long' => $request->long,
        'city' => $request->city,
        'country' => $request->country,
    ]);

    return response()->json(['success' => true]);
})->middleware('auth');

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

// Route::get('/external/rfid-scan', function(Request $request) {
//     $uidResult = $request->input('UIDresult');

//     \Cache::put('card', 'hey', 300);
//     abort(403);

//     $user = \App\Models\User::find(1);
//     $user->addOrUpdateMeta('rfid', $uidResult);
// });
Route::middleware(['web'])->post('/external/rfid-scan', function(Request $request) {
    $uidResult = $request->input('UIDresult');

    if (!$uidResult) {

        Cache::put('rfid-scanned-response', [
            'status' => 'danger',
            'message' => 'No UID request provided!',
            'card_id' => null,
            'rfid' => null,
        ], now()->addSeconds(5));

        return response()->json(['message' => 'No UID provided'], 400);
    }

    $card = Card::where('rfid', $uidResult)->first();

    if (!$card) {
        Cache::put('scanned_rfid', $uidResult, now()->addSeconds(5));

        Cache::put('rfid-scanned-response', [
            'status' => 'success',
            'message' => 'Card successfully scanned',
            'card_id' => null,
            'rfid' => $uidResult,
        ], now()->addSeconds(5));

        return response()->json(['message' => 'Card successfully scanned'], 200);
    }

    if($card->type == 'Staff') {
        $staff = $card->staff;
        if($staff) {
            $attendance = $staff->attendances()->whereNull('check_out')->latest()->first();
            if($attendance) {
                if(CashLog::where('status', true)->where('user_id', $staff->user->id)->exists()) {
                    Cache::put('rfid-scanned-response', [
                        'status' => 'danger',
                        'message' => 'Enter cash out amount before ending shift!',
                        'card_id' => $card->id,
                        'rfid' => $uidResult,
                    ], now()->addSeconds(5));

                    return response()->json(['message' => 'Enter cash out amount before ending shift!'], 200);
                }

                // SIGNING OFF
                $attendance->update([
                    'check_out' => \Carbon\Carbon::now()
                ]);

                Cache::put('rfid-scanned-response', [
                    'status' => 'success',
                    'message' => $staff->user->name . ' successfully logout!',
                    'card_id' => $card->id,
                    'rfid' => $uidResult,
                ], now()->addSeconds(5));

                return response()->json(['message' => $staff->user->name . ' successfully logout'], 200);
            } else {
                // SIGNING ON
                $attendance = \App\Models\Attendance::create([
                    'staff_id' => $staff->id,
                    'check_in' => \Carbon\Carbon::now()
                ]);

                Cache::put('rfid-scanned-response', [
                    'status' => 'success',
                    'message' => $staff->user->name . ' successfully login!',
                    'card_id' => $card->id,
                    'rfid' => $uidResult,
                ], now()->addSeconds(5));

                return response()->json(['message' => $staff->user->name . ' successfully login'], 200);
            }
        }
    }

    $dailySale = DailySale::where('card_id', $card->id)
        ->where('status', true)
        ->latest()
        ->first();

    if ($dailySale) {
        Cache::put('latest_rfid_scan', $dailySale->id);

        Cache::put('rfid-scanned-response', [
            'status' => 'success',
            'message' => 'Daily check-in found!',
            'card_id' => $card->id,
            'rfid' => $uidResult,
        ], now()->addSeconds(5));

        return response()->json(['message' => 'Daily sale found.'], 200);
    }

    Cache::put('rfid-scanned-response', [
        'status' => 'danger',
        'message' => 'RFID not found!',
        'card_id' => $card->id,
        'rfid' => $uidResult,
    ], now()->addSeconds(5));

    return response()->json(['message' => 'RFID not found.'], 404);
});
Route::get('/check-latest-rfid', function () {
    if (! auth()->check()) {
        return response()->json(['success' => false]);
    }

    $currentCashier = CashLog::getCurrentCashierUser();
    if (! $currentCashier || auth()->id() !== $currentCashier->id) {
        return response()->json(['success' => false]);
    }

    $id = Cache::pull('latest_rfid_scan'); // get & delete

    if (!$id) {
        return response()->json(['success' => false]);
    }

    return response()->json([
        'success' => true,
        'daily_sale_id' => $id,
    ]);
});
Route::post('/clear-rfid-cache', function () {
    if (! auth()->check()) {
        return response()->json(['success' => false], 403);
    }

    $currentCashier = CashLog::getCurrentCashierUser();
    if (! $currentCashier || auth()->id() !== $currentCashier->id) {
        return response()->json(['success' => false], 403);
    }

    Cache::forget('latest_rfid_scan');

    return response()->json([
        'success' => true,
    ]);
});
