<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/bikes",
     *   summary="All user's bikes",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Bike"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function bikes(Request $request, User $user)
    {
        $bikes = Bike::where('user_id', $user->id)->get();

        return response()->json($bikes);
    }

    /**
     * @OA\Post(
     *   tags={"Users"},
     *   path="/bikes",
     *   summary="Add bike's user",
     *   description="Ajouter un vélo du user connecté",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddBike"),
     *   @OA\Response(
     *     response=201,
     *     description="Vélo d'un user créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="bike created"
     *       ),
     *       @OA\Property(
     *         property="bike",
     *         ref="#/components/schemas/Bike"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function storeBike(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string'],
                'brand' => ['required', 'string'],
                'model' => ['required', 'string'],
                'bike_type_id' => ['required'],
                'date' => ['date']
            ],
            [
                'name.required' => 'Le nom est obligatoire',
                'name.string' => 'Le nom doit être une chaîne de caractères',
                'brand.required' => 'La marque est obligatoire',
                'brand.string' => 'La marque doit être une chaîne de caractères',
                'model.required' => 'Le modèle est obligatoire',
                'model.string' => 'Le modèle doit être une chaîne de caractères',
                'bike_type_id.required' => 'Le type est obligatoire',
                'date.date' => 'La date doit être une date valide',
            ]
        );

        // S'il y a une erreur dans le check
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'bike_type_id' => $request->bike_type_id,
            'date' => $request->date,
            'user_id' => $request->user()->id
        ];

        if ($request->weight) {
            $data[] = ['weight' => $request->weight];
        }

        if ($request->image) {
            // !Todo: Enregistrement de l'image dans le storage

            $data[] = ['image' => 'nom de l\'image.png'];
        }

        $bike = Bike::create($data);

        return response()->json([
            'message' => 'Bike created',
            'bike' => $bike
        ], 201);
    }

    //! A REVOIR
    public function images(Request $request, User $user)
    {
        $user_details = $user->load('postImages');

        return response()->json($user_details->postImages);
    }
}
