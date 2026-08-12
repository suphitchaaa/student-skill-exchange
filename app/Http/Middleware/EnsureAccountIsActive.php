<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs('account.suspended') || $request->user()?->status === 'active') {
            return $next($request);
        }

        // บัญชีถูกระงับต้องคง session ไว้เพื่อให้ผู้ใช้เลือกออกจากระบบเอง
        return redirect()->route('account.suspended');
    }
}
