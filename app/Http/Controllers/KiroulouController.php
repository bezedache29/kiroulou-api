<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Models\City;
use App\Models\Address;
use App\Models\Zipcode;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *   version="0.0.1",
 *   title="Kiroulou API Documentation",
 *   description="Documentation de l'API utilisée par l'application mobile KiRoulOu, faite avec Laravel",
 *   @OA\Contact(email="bezedache29@gmail.com"),
 *   @OA\License(
 *     name="Apache 2.0",
 *     url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *   )
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Server API KiRoulOu"
 * )
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Opérations d'authentification"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="Opérations en relation avec les utilisateurs"
 * )
 * 
 * @OA\Tag(
 *     name="Clubs",
 *     description="Opérations en relation avec les clubs"
 * )
 * 
 * @OA\Tag(
 *     name="Posts",
 *     description="Opérations en relation avec les articles users & clubs"
 * )
 * 
 * @OA\Tag(
 *     name="Hikes VTT",
 *     description="Opérations en relation avec les randonnées VTT"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Opérations en relation avec les paiements stripe"
 * )
 * 
 * @OA\Tag(
 *     name="Divers",
 *     description="Opérations diverses"
 * )
 * 
 * 
 * @OA\SecurityScheme(
 *   type="http",
 *   description="S'authentifier avec email & password pour récupérer un token d'authentification",
 *   name="Authorization",
 *   in="header",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   securityScheme="bearer_token",
 * )
 * 
 * @OA\Parameter(
 *   name="id",
 *   in="path",
 *   description="ID de la ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="club_id",
 *   in="path",
 *   description="ID du club",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="post_id",
 *   in="path",
 *   description="ID du post (uuid)",
 *   required=true,
 *   @OA\Schema(type="string")
 * )
 * 
 * @OA\Parameter(
 *   name="user_id",
 *   in="path",
 *   description="ID du user",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="page",
 *   in="path",
 *   description="Numéro de la page",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="type",
 *   in="path",
 *   description="Type de ressource",
 *   required=true,
 *   @OA\Schema(type="string")
 * )
 * 
 * @OA\Parameter(
 *   name="hike_id",
 *   in="path",
 *   description="ID de ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="bike_id",
 *   in="path",
 *   description="ID de ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="comment_id",
 *   in="path",
 *   description="ID de ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="image_id",
 *   in="path",
 *   description="ID de ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="trip_id",
 *   in="path",
 *   description="ID de ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Response(
 *   response=409,
 *   response="Conflit",
 *   description="Conflit de ressource",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Un compte avec cette adresse email existe déjà")
 *   )
 * )
 * 
 * @OA\Response(
 *   response=201,
 *   response="Created",
 *   description="Ressource créée",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="user created")
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=422,
 *   response="UnprocessableEntity",
 *   description="Ressource non traitée",
 *   @OA\JsonContent(
 *     @OA\Property(
 *       property="email",
 *       type="array",
 *       @OA\Items(),
 *       example={
 *         "L'adresse email est obligatoire",
 *       },
 *     )
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=403,
 *   response="Forbidden",
 *   description="Accès refusé à la ressource demandée",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="email et/ou mot de passe incorrect(s)")
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=404,
 *   response="NotFound",
 *   description="Ressource introuvable",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Resource not found")
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=401,
 *   response="Unauthorized",
 *   description="Accès non autorisé",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Unauthorized")
 *   )
 * ),
 */
class KiroulouController extends Controller
{
    /**
     * @OA\Post(
     *   tags={"Divers"},
     *   path="/addresses/isAlreadyExist",
     *   summary="Check if address already exist",
     *   description="Regarde si une adresse similaire existe déjà",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/CheckAddress"),
     *   @OA\Response(
     *     response=201,
     *     description="Address créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="isAlreadyExist",
     *         ref="#/components/schemas/Address"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=404, 
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function isAlreadyExist(CheckAddressRequest $request)
    {
        $city = City::where('name', $request->city)->first();
        $zipcode = Zipcode::where('code', $request->zipcode)->first();

        if (!$city || !$zipcode) {
            return response()->json(['isAlreadyExist' => false], 404);
        }

        $address = Address::where('street_address', $request->street_address)
            ->where('city_id', $city->id)
            ->where('zipcode_id', $zipcode->id)
            ->first();

        if (!$address) {
            return response()->json(['isAlreadyExist' => false], 404);
        }

        return response()->json(['isAlreadyExist' => $address], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Divers"},
     *   path="/addresses/create",
     *   summary="Create new address",
     *   description="Création d'une nouvelle adresse",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/NewAddress"),
     *   @OA\Response(
     *     response=201,
     *     description="Adresse créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="address created"
     *       ),
     *       @OA\Property(
     *         property="address",
     *         ref="#/components/schemas/Address"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function createAddress(StoreAddressRequest $request)
    {
        $city = City::where('name', $request->city)->first();
        if (!$city) {
            $city = City::create([
                'name' => $request->city
            ]);
        }
        $zipcode = Zipcode::where('code', $request->zipcode)->first();
        if (!$zipcode) {
            $zipcode = Zipcode::create([
                'code' => $request->zipcode
            ]);
        }

        $address = Address::create([
            'street_address' => $request->street_address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'region' => $request->region,
            'department' => $request->department,
            'department_code' => $request->department_code,
            'city_id' => $city->id,
            'zipcode_id' => $zipcode->id
        ]);

        return response()->json([
            'message' => 'address created',
            'address' => $address
        ], 201);
    }
}
