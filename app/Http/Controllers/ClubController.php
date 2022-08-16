<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Club;
use App\Models\User;
use App\Models\Address;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{
    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs",
     *   summary="Add Club",
     *   description="Création d'un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClub"),
     *   @OA\Response(
     *     response=201,
     *     description="Club créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="club created"
     *       ),
     *       @OA\Property(
     *         property="user_update",
     *         type="object",
     *         example={
     *           "is_club_admin": true,
     *           "club_id": 1
     *         },
     *         @OA\Property(
     *           property="is_club_admin",
     *           type="boolean",
     *           description="Le user devient admin du club qu'il vient de créer"
     *         ),
     *         @OA\Property(
     *           property="club_id",
     *           type="number",
     *           description="Le user est membre du club qu'il vient de créer"
     *         ),
     *       ),
     *       @OA\Property(
     *         property="club",
     *         type="array",
     *         description="Détails du club",
     *         @OA\Items(ref="#/components/schemas/Club")
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     description="Déjà dans un club",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Vous faites déjà partie d'un club !"
     *       )
     *     )
     *   ),
     * )
     */
    public function storeClub(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string'],
                'shortName' => ['string'],
                'street_address' => ['required', 'string'],
                'lat' => ['required'],
                'lng' => ['required'],
                'region' => ['required'],
                'department' => ['required'],
                'department_code' => ['required'],
                'city' => ['required', 'string'],
                'zipcode' => ['required', 'string'],
                'website' => ['string'],
                'organization' => ['required'],
                'avatar' => ['string'],
            ],
            [
                'name.required' => 'Le nom est obligatoire',
                'street_address.required' => 'L\'adresse est obligatoire',
                'city.required' => 'L\'adresse est obligatoire',
                'zipcode.required' => 'L\'adresse est obligatoire',
                'lat.required' => 'L\'adresse est obligatoire',
                'lng.required' => 'L\'adresse est obligatoire',
                'region.required' => 'L\'adresse est obligatoire',
                'department.required' => 'L\'adresse est obligatoire',
                'department_code.required' => 'L\'adresse est obligatoire',
                'organization.required' => 'L\'organisation est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // On check que le user n'est pas déjà dans un club
        $is_on_club = $request->user()->haveClub();

        if ($is_on_club) {
            return response()->json(["message" => "Vous faites déjà partie d'un club"], 422);
        }

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

        $create_address = [
            'street_address' => $request->street_address,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'region' => $request->region,
            'department' => $request->department,
            'department_code' => $request->department_code,
            'city_id' => $city->id,
            'zipcode_id' => $zipcode->id,
        ];

        $address = Address::create($create_address);

        $club = [
            'name' => $request->name,
            'address_id' => $address->id,
            'organization_id' => $request->organization,
        ];

        if ($request->website) {
            $club = array_merge($club, ['website' => $request->website]);
        }

        if ($request->avatar) {
            $club = array_merge($club, ["avatar" => $request->avatar]);
        }

        $club_created = Club::create($club);

        // On met à jour le user avec l'id du club et le fait qu'il soit admin du club
        $user_update = [
            "is_club_admin" => true,
            "club_id" => $club_created->id
        ];
        User::where('id', $request->user()->id)->update($user_update);

        // On récupère toutes les infos du club pour le retourner
        $club = Club::find($club_created->id);

        return response()->json(["message" => "club created", "user_update" => $user_update, "club" => $club], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs",
     *   summary="All clubs",
     *   description="Tous les clubs",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     required=false,
     *     description="Le nombre de clubs à récupérer",
     *     @OA\Schema(type="string"),
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubWithCounts")
     *     )
     *   )
     * )
     */
    public function clubs()
    {
        // $clubs = Club::paginate(1);
        $clubs = Club::withCount('members')->withCount('userFollows')->withCount('posts')->get();

        return response()->json($clubs, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/clubInformations",
     *   summary="Club's informations",
     *   description="Les informations du club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubInformations")
     *     )
     *   )
     * )
     */
    public function clubInformations(Club $club)
    {
        // $club = Club::with('members')->with('organization')->with('posts')->with('userJoinRequests')->findOrFail($club->id);
        return response()->json($club, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/clubPosts",
     *   summary="Club's posts",
     *   description="Le club et ses articles",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubPosts")
     *     )
     *   )
     * )
     */
    public function clubPosts(Club $club)
    {
        $club = Club::with('posts')->findOrFail($club->id);
        return response()->json($club, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/clubMembers",
     *   summary="Club's members",
     *   description="Le club et ses membres ainsi que les demandes d'adhésion",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubMembers")
     *     )
     *   )
     * )
     */
    public function clubMembers(Club $club)
    {
        $club = Club::with('members')->with('userJoinRequests')->findOrFail($club->id);
        return response()->json($club, 200);
    }

    /**
     * @OA\Post(
     *   path="/clubs/{club_id}/followOrUnfollow",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Follow/UnFollow club",
     *   description="Follow/UnFollow un club",
     *   tags={"Clubs"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function followOrUnfollow(Request $request, Club $club)
    {
        // On check si le user ne follow déjà pas le club
        $is_follow = DB::table('club_follows')->where('user_id', $request->user()->id)->where('club_id', $club->id)->first();

        // S'il follow déjà, on unfollow
        // Sinon on entre le user et le club dans la DB
        if ($is_follow) {
            $request->user()->clubFollows()->wherePivot('club_id', $club->id)->detach();
            $action = 'UnFollow';
        } else {
            $request->user()->clubFollows()->attach($club->id);
            $action = 'Follow';
        }

        return response()->json(["message" => $action], 201);
    }

    /**
     * @OA\Post(
     *   path="/clubs/{club_id}/requestToJoin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="User membership request",
     *   description="Demande d'adhésion au club",
     *   tags={"Clubs"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Demande déjà en cours de traitement",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Votre demande est déjà en traitement pour ce club"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function requestToJoin(Request $request, Club $club)
    {

        // On check si le user n'a pas déjà une demande en attente pour le club
        $is_request = DB::table('club_join_requests')->where('user_id', $request->user()->id)->where('club_id', $club->id)->first();

        if ($is_request) {
            return response()->json(["message" => "Votre demande est déjà en traitement pour ce club"], 403);
        }

        $request->user()->myMembershipRequests()->attach($club->id);

        return response()->json(["message" => "Request club to join it"], 201);
    }

    /**
     * @OA\Post(
     *   path="/clubs/{club_id}/acceptRequestToJoin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Accept the user's membership request",
     *   description="Acceptation de la demande d'adhésion d'un user au club",
     *   tags={"Clubs"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     description="Accept la demande d'adhésion du user dans le club",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Accept Membership Request"),
     *       @OA\Property(
     *         property="club",
     *         type="object",
     *         description="Détails du club",
     *         ref="#/components/schemas/Club"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=409,
     *     ref="#/components/responses/Conflit"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function acceptRequestToJoin(Request $request, Club $club)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => ['required'],
            ],
            [
                'user_id.required' => 'Le user_id est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // On check que le user n'est pas déjà dans un club
        $is_already_in_club = User::where('id', $request->user_id)->where('club_id', '!=', NULL)->first();

        if ($is_already_in_club) {
            return response()->json(["message" => "L'utilisateur est déjà membre dans un club"], 409);
        }

        // On met à jour le user avec le club id
        User::where('id', $request->user_id)->update(['club_id' => $club->id]);

        // On supprime toutes les autres demande du use qui sont en attente dans la table club_join_requests
        DB::table('club_join_requests')->where('user_id', $request->user_id)->delete();

        // On récupère le club à jour
        $club = Club::find($club->id);

        return response()->json([
            "message" => "Accept Membership Request",
            "club" => $club
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/clubs/{club_id}/denyRequestToJoin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Deny the user's membership request",
     *   description="Refus de la demande d'adhésion d'un user au club",
     *   tags={"Clubs"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     description="Refuse la demande d'adhésion du user dans le club",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Deny Membership Request"),
     *       @OA\Property(
     *         property="club",
     *         type="object",
     *         description="Détails du club",
     *         ref="#/components/schemas/Club"
     *       )
     *     )
     *   ),
     *     @OA\Response(
     *     response=409,
     *     ref="#/components/responses/Conflit"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function denyRequestToJoin(Request $request, Club $club)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => ['required'],
            ],
            [
                'user_id.required' => 'Le club_id est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // On check que le user n'est pas déjà dans un club
        $is_already_in_club = User::where('id', $request->user_id)->where('club_id', '!=', NULL)->first();

        if ($is_already_in_club) {
            return response()->json(["message" => "L'utilisateur est déjà membre dans un club"], 409);
        }

        // On supprime la demande du user qui est en attente dans la table club_join_requests pour le club
        DB::table('club_join_requests')->where('user_id', $request->user_id)->where('club_id', $club->id)->delete();

        // On récupère le club à jour
        $club = Club::find($club->id);

        return response()->json([
            "message" => "Deny Membership Request",
            "club" => $club
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/showJoinRequests",
     *   summary="Show user club membership requests",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(
     *           property="user_id",
     *           type="number",
     *           example=10005
     *         ),
     *         @OA\Property(
     *           property="club_id",
     *           type="number",
     *           example=1
     *         ),
     *         @OA\Property(
     *           property="created_at",
     *           type="string",
     *           example="2022-08-15 14:25:01"
     *         ),
     *         @OA\Property(
     *           property="updated_at",
     *           type="string",
     *           example=null
     *         ),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function showJoinRequests(Request $request, Club $club)
    {
        $join_requests = DB::table('club_join_requests')->where('club_id', $club->id)->get();

        return response()->json($join_requests, 200);
    }

    /**
     * @OA\Post(
     *   path="/clubs/{club_id}/changeAdmin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/ChangeAdmin"),
     *   summary="Change club admin",
     *   description="Change d'admin pour le club",
     *   tags={"Clubs"},
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
    public function changeAdmin(Request $request, Club $club)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => ['required'],
            ],
            [
                'user_id.required' => 'Le user_id est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::findOrFail($request->user_id);

        // On check que le futur admin est bien dans le club
        if ($user->club_id == $club->id) {
            // On retire l'admin au user connecté
            $request->user()->is_club_admin = false;
            $request->user()->save();

            // On met le user souhaité admin du club
            $user->is_club_admin = true;
            $user->save();
        }

        return response()->json(['message' => 'admin changed'], 201);
    }
}
