<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SalesRep;
use Illuminate\Support\Facades\Auth;

class AuthorizeSalesRep
{
    public function handle(Request $request, Closure $next)
    {
        $salesRepParam = $request->route('salesRep')
            ?? $request->route('sales_rep')
            ?? $request->route('salesrep')
            ?? $request->route('sales-rep');
        $salesRep = $salesRepParam instanceof SalesRep
            ? $salesRepParam
            : SalesRep::where('id', $salesRepParam)->first();

        if (Auth::user()->role === 'admin') {
            return $next($request);
        }
        if (!$salesRep || Auth::id() !== $salesRep->user_id) {
            abort(403);
        }

        return $next($request);
    }
}
