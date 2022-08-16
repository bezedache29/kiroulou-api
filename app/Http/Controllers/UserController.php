<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PostUserImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *   tags={"Users"},
     *   path="/users/bikes",
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
                'date' => ['date'],
                'weight' => ['string'],
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
                'weight.string' => 'Le poids doit être une chaîne de caractères',
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
            $data['weight'] = $request->weight;
        }

        if ($request->image) {
            //TODO: Enregistrement de l'image dans le storage

            $data['image'] = 'image-name.png';
        }

        $bike = Bike::create($data);

        $bike = Bike::findOrFail($bike->id);

        return response()->json([
            'message' => 'Bike created',
            'bike' => $bike
        ], 201);
    }

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

        return response()->json($bikes, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/profileImages",
     *   summary="Last 4 profile images",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/PostUserImage"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function profileImages(User $user)
    {
        $images = PostUserImage::where('user_id', $user->id)->orderBy('id', 'DESC')->limit(4)->get();

        return response()->json($images, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/allImages",
     *   summary="All user's images",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/PostUserImage"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function allImages(User $user)
    {
        $images = PostUserImage::where('user_id', $user->id)->orderBy('id', 'DESC')->get();

        return response()->json($images, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/followedClubs",
     *   summary="Clubs followed by the user",
     *   description="Les clubs suivis par le user",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(property="user_id", type="number", example=10005, description="Id du user qui suis le club"),
     *         @OA\Property(property="club_id", type="number", example=1, description="Id du club qui est suivis"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function followedClubs(User $user)
    {
        $clubs = DB::table('club_follows')->where('user_id', $user->id)->get();
        return response()->json($clubs, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/followedUsers",
     *   summary="Users followed by the user",
     *   description="Les users suivis par le user",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(property="user_follower_id", type="number", example=10005, description="Id du user qui suis"),
     *         @OA\Property(property="user_followed_id", type="number", example=1, description="Id du user qui est suis"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function followedUsers(User $user)
    {
        $clubs = DB::table('follow_users')->where('user_follower_id', $user->id)->get();
        return response()->json($clubs, 200);
    }

    /**
     * @OA\Post(
     *   path="/users/{user_id}/followOrUnfollow",
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   summary="Follow/Unfollow user",
     *   description="Follow/Unfollow un user",
     *   tags={"Users"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function followOrUnfollow(Request $request, User $user)
    {
        // On check si le user ne follow déjà pas le user a follow
        $is_follow = DB::table('follow_users')
            ->where('user_follower_id', $request->user()->id)
            ->where('user_followed_id', $user->id)
            ->first();

        // S'il follow déjà, on unfollow
        // Sinon on entre le user_follower et le user_followed dans la DB
        if ($is_follow) {
            $request->user()
                ->followings()
                ->wherePivot('user_follower_id', $request->user()->id)
                ->wherePivot('user_followed_id', $user->id)
                ->detach();
            $action = 'UnFollow';
        } else {
            $request->user()->followings()->attach($user->id);
            $action = 'Follow';
        }

        return response()->json(["message" => $action], 201);
    }
}
