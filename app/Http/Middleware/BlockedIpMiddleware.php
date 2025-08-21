<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesRepLoginIp;

class BlockedIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // فقط طبّقيه على المندوبين
        if ($user && $user->role === 'sales_rep') {
            $ip = $request->ip();

            $loginIp = SalesRepLoginIp::where('sales_rep_id', $user->id)
                ->where('ip_address', $ip)
                ->first();

            if ($loginIp && !$loginIp->is_allowed) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'تم حظر هذا الجهاز من قبل الإدارة. الرجاء التواصل مع الدعم.',
                ]);
            }
        }

        return $next($request);
    }
}
