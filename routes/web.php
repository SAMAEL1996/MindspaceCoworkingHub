<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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

Route::get('/flexi', function() {
    $flexi = null;
    $time = [];
    $error = false;
    
    if(request()->has('contact')) {
        $decrypted = Crypt::decryptString(request('contact'));
        $flexi = \App\Models\FlexiUser::where('contact_no', $decrypted)->first();

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
        'contact' => 'required'
    ]);

    return redirect()->route('flexi.remaining-time', ['contact' => Crypt::encryptString(request('contact'))]);
})->name('flexi.remaining-time');