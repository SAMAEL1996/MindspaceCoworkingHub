<?php

use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

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

Route::get('/flexi/{contact}', function() {
    $flexi = \App\Models\FlexiUser::where('contact_no', request('contact'))->first();

    if(!$flexi->status) {
        return abort(404);
    }
    
    $time = $flexi->getRemainingTimeArray();

    return view('frontend.flexi.show', compact('flexi', 'time'));
})->name('flexi.remaining-time');
