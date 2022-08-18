<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Http\Request;

class IsAdminClubOrPremium1
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

        if (($sub && $sub->subscriptionType->name == 'Premium 1') || $request->user()->is_club_admin == true) {
            return $next($request);
        }

        return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
    }
}
