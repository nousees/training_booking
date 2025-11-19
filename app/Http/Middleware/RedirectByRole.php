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
                    return redirect()->route('admin.dashboard');
                case 'trainer':
                    return redirect()->route('trainer-panel.schedule');
                case 'client':
                default:
                    return redirect()->route('trainers');
            }
        }

        return $next($request);
    }
}


