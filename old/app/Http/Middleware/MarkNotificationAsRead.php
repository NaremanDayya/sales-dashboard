<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationAsRead
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->query('nid');
        $user = $request->user();

        if ($id && $user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification && $notification->read_at === null) {
                $notification->markAsRead();
            }
        }

        return $next($request);
    }
}
