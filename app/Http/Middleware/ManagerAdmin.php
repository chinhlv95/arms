<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper;

class ManagerAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Helper::check_permission(1) == 1 || Helper::check_permission(2) == 1){// manager or admin
            return $next($request);
        }
        else
            return redirect()->route('get.error');
    }
}
