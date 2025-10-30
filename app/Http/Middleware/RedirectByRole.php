<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            switch ($user->role) {
                case 'owner':
                    return redirect()->route('owner.gyms.index');
                case 'manager':
                    return redirect()->route('manager.trainers.index');
                case 'user':
                    return redirect()->route('user.trainings.index');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}


