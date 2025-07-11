<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
                            $user = Auth::user();
                                             
        if (!Auth::check()) {
            return redirect('/admin/login');
        }
        
         if ($user->role === 'user') {
            return redirect('/users/subscriptions');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}

