<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPremium2
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
                if (count($stripe_customer->subscriptions->data) > 1) {
                    foreach ($stripe_customer->subscriptions->data as $sub) {
                        if ($sub->cancel_at_period_end && $sub->status == 'active' && $sub->plan->nickname == 'Premium 2') {
                            $premium_name = $sub->plan->nickname;
                        }
                    }
                } else {
                    $premium_name = $stripe_customer->subscriptions->data[0]->plan->nickname;
                }

                if ($premium_name == 'Premium 2') {
                    return $next($request);
                }
            }

            return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
        }

        return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
    }
}
