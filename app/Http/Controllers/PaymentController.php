<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Subs;
use App\Models\User;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\StripeClient;
use Stripe\Subscription;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
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

    /**
     * @OA\Get(
     *   tags={"Payments"},
     *   path="/subscriptions/plans",
     *   summary="All subs plans",
     *   description="Les plans d'abonnement",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(
     *           property="id",
     *           type="string",
     *           example="price_1LZjEKGofnt4tufZaVgerppF",
     *           description="id du prix"
     *         ),
     *         @OA\Property(
     *           property="active",
     *           type="boolean",
     *           example=true,
     *           description="plan actif ou non"
     *         ),
     *         @OA\Property(
     *           property="amount",
     *           type="number",
     *           example=499,
     *           description="prix en centimes"
     *         ),
     *         @OA\Property(
     *           property="nickname",
     *           type="string",
     *           example="Premium 2",
     *           description="Nom du plan"
     *         ),
     *         @OA\Property(
     *           property="product",
     *           type="string",
     *           example="prod_MIJlTw1G77xNbZ",
     *           description="id du produit du plan"
     *         ),
     *         @OA\Property(
     *           property="interval",
     *           type="string",
     *           example="month",
     *           description="Durée du sub avant prochain debit"
     *         ),
     *         @OA\Property(
     *           property="currency",
     *           type="string",
     *           example="eur",
     *           description="Devise du plan"
     *         ),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function plans()
    {
        $plans = $this->stripe->plans->all();

        return response()->json($plans->data, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Payments"},
     *   path="/subscriptions/subscribe",
     *   summary="Create and get sub payment intent",
     *   description="Cré et récupère l'intention de payment sub depuis stripe du user",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"price_id"},
     *       @OA\Property(property="price_id", type="string", example="price_1LZjCyGofnt4tufZ9NWGMSIF"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="subscriptionId", type="string", example="sub_1Labl7Gofnt4tufZx612e5vZ"),
     *       @OA\Property(property="clientSecret", type="string", example="pi_3LafxdGofnt4tufZ1dSWNIAV_secret_KvNoMg4ye3WFrBWka9IDZ632J"),
     *       @OA\Property(property="ephemeralKey", type="string", example="ek_test_YWNjdF8xTFZBekpHb2ZudDR0dWZaLFJjQk5jQnhTVVpQNVhZY2lKY1JqbERudnJkSDN5dGw_00ol5BnsEL"),
     *       @OA\Property(property="customer", type="string", example="cus_MJEDCDFb8Wot43"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   )
     * )
     */
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

    /**
     * @OA\Post(
     *   tags={"Payments"},
     *   path="/subscriptions/cancel",
     *   summary="Cancel sub",
     *   description="Annulation de l'abonnement du user connecté",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Déjà annulé",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="already canceled")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function cancel(Request $request)
    {
        if ($request->user()->stripe_customer_id == "0") {
            return response()->json(['message' => 'no subscriptions'], 404);
        }

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

        $user = User::with('address')->findOrFail($request->user()->id);

        return response()->json([
            'message' => 'sub canceled',
            'user' => $user
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Payments"},
     *   path="/subscriptions/check",
     *   summary="check subs",
     *   description="Check si le user connecté est abonné et retourne l'id du sub",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="string",
     *       required={"name"},
     *       example="sub_1Labl7Gofnt4tufZx612e5vZ"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Déjà annulé",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="already canceled")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
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

        return response()->json(['message' => 'resource not found'], 404);
    }

    /**
     * @OA\Post(
     *   tags={"Payments"},
     *   path="/subscriptions/deleteFails",
     *   summary="Delete sub incomplet",
     *   description="Supprime les intentions de sub non complétés",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
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

        return response()->json(['message' => 'ok'], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Payments"},
     *   path="/subscriptions/delete",
     *   summary="Cancel sub",
     *   description="Anulle l'abonnement actuel du user",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"subscription_id"},
     *       @OA\Property(property="subscription_id", type="string", example="sub_1Labl7Gofnt4tufZx612e5vZ"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function deleteSubscription(Request $request)
    {
        $this->stripe->subscriptions->cancel(
            $request->subscription_id,
            []
        );

        return response()->json(['message' => 'subscription canceled'], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Payments"},
     *   path="/billing",
     *   summary="billing's user",
     *   description="Récupère les informations de paiement du user depuis stripe",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       description="payment infos",
     *       @OA\Property(
     *         property="sub",
     *         description="sub",
     *         @OA\Property(property="cancel_at_period_end", type="boolean", example=false),
     *         @OA\Property(property="current_period_end", type="number", example=1664876127, description="timestamp"),
     *         @OA\Property(property="current_period_start", type="number", example=1662284127, description="timestamp"),
     *         @OA\Property(
     *           property="plan",
     *           description="plan",
     *           @OA\Property(property="nickname", type="string", example="Premium 2"),
     *           @OA\Property(property="amount", type="number", example=499, description="Prix en centimes"),
     *           @OA\Property(property="interval", type="string", example="month"),
     *         ),
     *         @OA\Property(property="status", type="string", example="active")
     *       ),
     *       @OA\Property(
     *         property="payment_method",
     *         description="payment method",
     *         @OA\Property(property="type", type="string", example="card"),
     *         @OA\Property(
     *           property="card",
     *           description="card",
     *           @OA\Property(property="last4", type="string", example="4242", description="4 dernier chiffres de la CB"),
     *           @OA\Property(property="brand", type="string", example="visa"),
     *         ),
     *       ),
     *       @OA\Property(
     *         property="latest_invoice",
     *         description="facture",
     *         @OA\Property(property="invoice_pdf", type="string", example="https://pay.stripe.com/invoice/acct_1LVAzJGofnt4tufZ/test_YWNjdF8xTFZBekpHb2ZudDR0dWZaLF9NTXpESXd6TE5ZNjdtTEdvU3FPRzNsbG1FOThvUTZYLDUyODM0MjU00200yZ5KsDzF/pdf?s=ap"),
     *       ),
     *       @OA\Property(
     *         property="latest_charge",
     *         description="reçu",
     *         @OA\Property(property="receipt_url", type="string", example="https://pay.stripe.com/receipts/invoices/CAcaFwoVYWNjdF8xTFZBekpHb2ZudDR0dWZaKM6r0pgGMgZUWFXIY3A6LBbqp0lys5pQR1HLSE8h3Kip2cSyn8liNpqS8Pk30znHmvH8AkC823zLG_ud?s=ap"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function billing(Request $request)
    {
        if ($request->user()->stripe_customer_id == "0") {
            return response()->json(['message' => 'customer not found'], 404);
        }

        $stripe_customer = $this->stripe->customers->retrieve($request->user()->stripe_customer_id, ['expand' => ['subscriptions']]);

        if (empty($stripe_customer->subscriptions->data)) {
            return response()->json(['message' => 'no subscriptions'], 404);
        }

        $sub = $stripe_customer->subscriptions->data[0];

        $payment_method = $this->stripe->paymentMethods->retrieve(
            $sub->default_payment_method,
            []
        );

        $lastest_invoice = $this->stripe->invoices->retrieve(
            $sub->latest_invoice,
            []
        );

        $latest_charge = $this->stripe->charges->retrieve(
            $lastest_invoice->charge,
            []
        );

        $payment_intent = $this->stripe->paymentIntents->retrieve(
            $latest_charge->payment_intent,
            []
        );

        return response()->json([
            'sub' => $sub,
            'payment_method' => $payment_method,
            'latest_invoice' => $lastest_invoice,
            'latest_charge' => $latest_charge,
            'payment_intent' => $payment_intent
        ], 200);
    }

    /**
     * Permet de créer ou de récupérer un customer stripe du user connecté
     */
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

    /**
     * Permet de créer un clé éphémère durant la création d'une intention de paiement
     */
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

    public function test(Request $request)
    {
        $stripe = new \Stripe\StripeClient(
            'sk_test_51LVAzJGofnt4tufZDf3SLBqrgWHFtwvG5eeA9nimfwYgFjYTRFUKCA3xQNnqwpwMDrnW4lyrFbLF8w7A6GSLq7Zu00h2OkOj7P'
        );
        $test = $stripe->subscriptions->all(['limit' => 3]);

        return response()->json($test, 200);
    }
}
