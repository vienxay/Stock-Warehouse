<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403, 'ບໍ່ມີສິດເຂົ້າໃຊ້ໜ້ານີ້');
        }

        return $next($request);
    }
}
