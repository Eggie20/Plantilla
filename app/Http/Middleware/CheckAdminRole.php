<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);
    }
}
