<?php

namespace App\Http\Middleware;

use Closure;
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
        if ($request->user()->stripe_customer_id != "0") {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SK'));
            $stripe_customer = $stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);
            if (!empty($stripe_customer->subscriptions->data)) {
                $premium_name = $stripe_customer->subscriptions->data[0]->plan->nickname;

                if (($premium_name == 'Premium 1') || $request->user()->is_club_admin == true) {
                    return $next($request);
                }
            }

            return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
        }

        return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
    }
}
