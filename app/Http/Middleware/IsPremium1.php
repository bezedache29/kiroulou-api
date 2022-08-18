<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Http\Request;

class IsPremium1
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $sub = Subscription::where('user_id', $request->user()->id)->where('end_at', '>=', Carbon::now())->first();

        if ($sub) {

            $type = $sub->subscriptionType->name;

            if ($type == 'Premium 1') {
                return $next($request);
            }

            return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
        }

        return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
    }
}
