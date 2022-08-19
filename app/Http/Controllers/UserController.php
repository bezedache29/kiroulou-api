<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBikeRequest;
use App\Http\Requests\StorePostUserRequest;
use Carbon\Carbon;
use App\Models\Bike;
use App\Models\User;
use App\Models\Subscription;
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
     *   
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function storeBike(StoreBikeRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;

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
    public function bikes(User $user)
    {
        $bikes = Bike::where('user_id', $user->id)->get();

        return response()->json($bikes, 200);
    }

    /**
     * @OA\Put(
     *   tags={"Users"},
     *   path="/users/bikes/{bike_id}",
     *   summary="Update user bike",
     *   description="Modifier un vélo du user connecté",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddBike"),
     *   @OA\RequestBody(ref="#/components/requestBodies/bike_id"),
     *   @OA\Response(
     *     response=201,
     *     description="Vélo du user modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="bike updated"
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
    public function updateBike(StoreBikeRequest $request, Bike $bike)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;

        if ($request->weight) {
            $data['weight'] = $request->weight;
        }

        if ($request->image) {
            //TODO: Enregistrement de l'image dans le storage

            $data['image'] = 'image-name.png';
        }

        $bike->update($data);

        return response()->json([
            'message' => 'Bike updated',
            'bike' => $bike
        ], 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Users"},
     *   path="/users/bikes/{bike_id}",
     *   summary="Delete bike",
     *   @OA\Parameter(ref="#/components/parameters/bike_id"),
     *   @OA\Response(
     *     response=201,
     *     description="Bike deleted",
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     ref="#/components/responses/Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function deleteBike(Bike $bike)
    {
        $bike->delete();

        return response()->json(['message' => 'bike deleted'], 201);
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
     *   path="/users/{user_id}/allImages?page={page}",
     *   summary="All user's images",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
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
        $images = PostUserImage::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(5)->items();

        return response()->json($images, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/followedClubs?page={page}",
     *   summary="Clubs followed by the user",
     *   description="Les clubs suivis par le user",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubWithCounts"),
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
        // On récupère la table pivot entre user et club pour les clubFollows. On compte les membres et les posts grace aux relations dans le Club et on pagine le resultat
        $clubs = $user->clubFollows()
            ->withCount(['members'])
            ->withCount(['userFollows'])
            ->withCount(['posts'])
            ->paginate(10)
            ->items();

        return response()->json($clubs, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/followedUsers?page={page}",
     *   summary="Users followed by the user",
     *   description="Les users suivis par le user",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/UserDetailsCount"),
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
        $users = $user->followings()
            ->withCount(['followers'])
            ->withCount(['posts'])
            ->withCount(['bikes'])
            ->paginate(10)
            ->items();

        return response()->json($users, 200);
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
