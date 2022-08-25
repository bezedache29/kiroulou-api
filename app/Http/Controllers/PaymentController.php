<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Subs;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Models\SubscriptionType;
use App\Http\Requests\StoreSubRequest;
use App\Http\Requests\SubscribeRequest;
use Stripe\Exception\InvalidRequestException;

class PaymentController extends Controller
{
    public $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(
            env('STRIPE_SK')
        );

        Stripe::setApiKey(env('STRIPE_SK'));
    }

    public function plans()
    {
        $plans = $this->stripe->plans->all();

        return response()->json($plans, 200);
    }

    public function types()
    {
        $types = SubscriptionType::all();

        return response()->json($types, 200);
    }

    public function customer($user)
    {
        // On check si un client stripe exist déjà via l'id customer récupérer avec l'API
        try {
            $is_customer_exist = Customer::retrieve(
                $user->stripe_customer_id,
                []
            );

            return $is_customer_exist;
        } catch (InvalidRequestException $e) {
            // Si le client Stripe n'existe pas, on le cré
            $customer = Customer::create([
                'email' => $user->email,
                'name' => ($user->firstname && $user->firstname) ? $user->firstname . ' ' . $user->firstname : null,
                'description' => 'Utilisateur KRO avec mail : ' . $user->email,
                'address' => [
                    'city' => $user->address != null ? $user->address->city->name : null,
                    'line1' => $user->address != null ? $user->address->street_address : null,
                    'postal_code' => $user->address != null ? $user->address->zipcode->code : null,
                    'country' => 'FR'
                ]
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();

            return $customer;
        }
    }

    public function ephemeralKey($customer)
    {
        $ephemeralKey = EphemeralKey::create(
            [
                'customer' => $customer->id,
            ],
            [
                'stripe_version' => '2022-08-01',
            ]
        );

        return $ephemeralKey;
    }

    // Non utilisé en V1
    public function simplePayment(Request $request)
    {
        $customer = $this->customer($request->user());

        $ephemeralKey = $this->ephemeralKey($customer);

        $paymentIntent = PaymentIntent::create([
            'amount' => 'price * 100', // PRICE * 100 recu de la request
            'currency' => 'eur',
            'customer' => $customer->id,
            'automatic_payment_methods' => [
                'enabled' => 'true',
            ],
            'payment_method_types' => ['card'],
            'receipt_email' => $request->user()->email
        ]);

        return response()->json([
            'pi' => $paymentIntent->id,
            'clientSecret' => $paymentIntent->client_secret,
            'ephemeralKey' => $ephemeralKey->secret,
            'customer' => $customer->id,
        ], 200);
    }

    public function subscribe(SubscribeRequest $request)
    {
        $customer = $this->customer($request->user());

        $ephemeralKey = $this->ephemeralKey($customer);

        $subscription = Subscription::create([
            'customer' => $this->customer($request->user()),
            'items' => [[
                'price' => $request->price_id,
            ]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Le clientSecret est important pour indiquer l'intention de paiement pour la suite des opéarations
        return response()->json([
            'subscriptionId' => $subscription->id,
            'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
            'ephemeralKey' => $ephemeralKey->secret,
            'customer' => $customer->id,
        ], 200);
    }

    // Pas d'utitlité immédiate pour l'app mobile
    // Permet de stocker les infos des achats des users en DB
    public function create(StoreSubRequest $request)
    {
        // On check s'il y a un abonnement en cours a annuler
        if ($request->sub_in_progress_id) {
            Subscription::update(
                $request->sub_in_progress_id,
                [
                    'cancel_at_period_end' => true,
                ]
            );
        }

        $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);

        foreach ($stripe_customer->subscriptions->data as $sub) {
            if (!$sub->cancel_at_period_end && $sub->status !== 'active') {
                $sub->delete();
            }
        }

        $subscription = Subscription::retrieve(
            $request->subscription_id,
            []
        );

        $data = [
            'stripe_subscription_id' => $subscription->id,
            'user_id' => $request->user()->id,
            'start_at' => Carbon::createFromTimestamp($subscription->start_date)->toDateTimeString(),
            'end_at' => Carbon::createFromTimestamp($subscription->current_period_end)->toDateTimeString(),
            'cancel_at_period_end' => $subscription->cancel_at_period_end,
            'default_payment_method_id' => $subscription->default_payment_method,
            'latest_invoice_id' => $subscription->latest_invoice,
            'status' => $subscription->status,
            'subscription_type_id' => $request->subscription_type_id
        ];

        $sub = Subs::create($data);

        return response()->json(['message' => 'subs created', 'sub' => $subscription, 'id' => $request->sub_in_progress_id], 201);
    }

    public function cancel(Request $request)
    {
        // Le premier élément du tableau est le dernier sub acheté
        $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);

        if (empty($stripe_customer->subscriptions->data)) {
            return response()->json(['message' => 'no subscriptions'], 404);
        }

        if ($stripe_customer->subscriptions->data[0]->cancel_at_period_end) {
            return response()->json(['message' => 'already canceled'], 403);
        }

        $stripe_subscription = $stripe_customer->subscriptions->data[0];

        // On update l'abonnement pour lui dire d'arreter a la fin du temps imparti
        Subscription::update(
            $stripe_subscription->id,
            [
                'cancel_at_period_end' => true,
            ]
        );

        return response()->json(['message' => 'sub canceled'], 201);
    }

    public function check(Request $request)
    {
        if ($request->user()->stripe_customer_id == "0") {
            return response()->json(['message' => 'customer not found'], 404);
        }

        // Le premier élément du tableau est le dernier sub acheté
        $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);

        if (empty($stripe_customer->subscriptions->data)) {
            return response()->json(['message' => 'no subscriptions'], 404);
        }

        foreach ($stripe_customer->subscriptions->data as $sub) {
            if (!$sub->cancel_at_period_end && $sub->status === 'active') {
                return response()->json($sub->id, 200);
            }

            if ($sub->cancel_at_period_end && $sub->status === 'active') {
                return response()->json(['message' => 'already canceled'], 403);
            }
        }
    }

    public function deleteFailsSubs(Request $request)
    {
        if ($request->user()->stripe_customer_id == "0") {
            return response()->json(['message' => 'customer not found'], 404);
        }

        // Le premier élément du tableau est le dernier sub acheté
        $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);

        if (empty($stripe_customer->subscriptions->data)) {
            return response()->json(['message' => 'no subscriptions'], 404);
        }

        foreach ($stripe_customer->subscriptions->data as $sub) {
            if (!$sub->cancel_at_period_end && $sub->status !== 'active') {
                $sub->delete();
            }
        }
    }

    public function deleteSubscription(Request $request)
    {
        $this->stripe->subscriptions->cancel(
            $request->subscription_id,
            []
        );

        return response()->json(['message' => 'subscription canceled'], 201);
    }

    public function subscriptions(Request $request)
    {
        // Le premier élément du tableau est le dernier sub acheté
        // $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);
        // $stripe_subscriptions = $stripe_customer->subscriptions->data;

        // return response()->json($stripe_subscriptions, 200);
    }

    public function subscription(Request $request)
    {
        // $this->stripe->invoices->search([
        //     'query' => 'customer:' . $request->user()->stripe_customer_id,
        // ]);
    }

    public function invoices(Request $request)
    {
        if ($request->user()->stripe_customer_id == "0") {
            return response()->json(['message' => 'Customer inexistant'], 404);
        }

        $id = strval($request->user()->stripe_customer_id);
        $invoices = $this->stripe->invoices->search([
            'query' => 'customer:\'' . $id . '\'',
        ]);

        return response()->json($invoices, 200);
    }
}
