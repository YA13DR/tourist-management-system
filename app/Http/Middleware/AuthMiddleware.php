<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if(!Filament::auth()->check() ){
        //     abort(403);
        //  }
        // return $next($request);

        $user = Filament::auth()->user();

        if (! $user) {
            abort(403); // لم يسجل الدخول
        }

        // تحقق من الدور
        if ($user->role === 'sub_admin' && $user->section === 'restaurant') {
            return redirect()->to(Filament::getPanel('restaurantSubAdmin')->getUrl());
        }
        if ($user->role === 'admin' && $user->section === 'restaurant') {
            return redirect()->to(Filament::getPanel('restaurantAdmin')->getUrl());
        }
        if ($user->role === 'sub_admin'&& $user->section === 'hotel') {
            return redirect()->to(Filament::getPanel('hotelSubAdmin')->getUrl());
        }
        if ($user->role === 'admin'&& $user->section === 'hotel') {
            return redirect()->to(Filament::getPanel('hotelAdmin')->getUrl());
        }
        if ($user->role === 'sub_admin'&& $user->section === 'tour') {
            return redirect()->to(Filament::getPanel('tourSubAdmin')->getUrl());
        }
        
        if ($user->role === 'admin'&& $user->section === 'tour') {
            return redirect()->to(Filament::getPanel('tourAdmin')->getUrl());
        }
        if ($user->role === 'sub_admin'&& $user->section === 'travel') {
            return redirect()->to(Filament::getPanel('travelSubAdmin')->getUrl());
        }
        
        if ($user->role === 'admin'&& $user->section === 'travel') {
            return redirect()->to(Filament::getPanel('travelAdmin')->getUrl());
        }
        
        if ($user->role === 'admin') {
            return redirect()->to(Filament::getPanel('admin')->getUrl());
        }

        return $next($request); 
    }
}
