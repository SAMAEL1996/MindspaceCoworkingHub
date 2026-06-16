<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FlexiUser;
use Illuminate\Support\Facades\Request;

class TimeTrackController extends Controller
{
    public function daily()
    {
        return view('frontend.daily.show');
    }

    public function flexi()
    {
        return view('frontend.flexi.show');
    }

    public function monthly()
    {
        return view('frontend.monthly.show');
    }
}