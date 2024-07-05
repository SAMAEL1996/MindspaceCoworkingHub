<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckStaffShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        Session::put('on_shift', false);

        if($user && $user->staff) {
            $staff = $user->staff;
            $attendance = $staff->attendances()->latest()->first();
    
            if($attendance && !$attendance->check_out) {
                Session::put('on_shift', true);
            }
        }

        return $next($request);
    }
}
