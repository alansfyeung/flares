<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

class ModifyDecorations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $requester = $request->user();
        if ($requester->access_level < User::ACCESS_CREATE) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No permission to modify decorations'
                ], 403);
            } else {
                abort(403);
            }
        }

        return $next($request);
    }
}
