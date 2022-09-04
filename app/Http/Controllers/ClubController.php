<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\City;
use App\Models\Club;
use App\Models\User;
use App\Models\Address;
use App\Models\HikeVtt;
use App\Models\Zipcode;
use App\Models\ClubPost;
use App\Models\HikeVttHype;
use App\Models\HikeVttTrip;
use App\Models\ClubPostLike;
use App\Models\HikeVttImage;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\ClubPostImage;
use App\Models\ClubPostComment;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreClubRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;

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
    public function storeClub(StoreClubRequest $request)
    {
        // On check que le user n'est pas déjà dans un club
        $is_on_club = $request->user()->haveClub();

        if ($is_on_club) {
            return response()->json(["club" => "Vous faites déjà partie d'un club"], 409);
        }

        $club = [
            'name' => $request->name,
            'address_id' => $request->address_id,
            'organization_id' => $request->organization_id,
            'short_name' => $request->short_name,
            'website' => $request->website
        ];

        $club_created = Club::create($club);

        // On met à jour le user avec l'id du club et le fait qu'il soit admin du club
        $user_update = [
            "is_club_admin" => true,
            "club_id" => $club_created->id
        ];
        // User::where('id', $request->user()->id)->update($user_update);

        // $user = User::with('address')->findOrFail($request->user()->id);

        // On récupère toutes les infos du club pour le retourner
        $club = Club::find($club_created->id);

        return response()->json([
            "message" => "club created",
            "user_update" => $user_update,
            "club" => $club
        ], 201);
    }

    public function deleteClub(Club $club)
    {
        // On check et delete l'ancienne image si elle existe
        if (Storage::exists($club->avatar)) {
            Storage::delete($club->avatar);
        }

        ClubPostImage::where('club_id', $club->id)->delete();
        DB::table('club_follows')->where('club_id', $club->id)->delete();
        DB::table('club_join_requests')->where('club_id', $club->id)->delete();
        $posts = ClubPost::where('club_id', $club->id)->get();
        foreach ($posts as $post) {
            ClubPostComment::where('club_post_id', $post->id)->delete();
            ClubPostLike::where('club_post_id', $post->id)->delete();
        }
        ClubPost::where('club_id', $club->id)->delete();
        $hikes = HikeVtt::where('club_id', $club->id)->get();
        foreach ($hikes as $hike) {
            HikeVttHype::where('hike_vtt_id', $hike->id)->delete();
            HikeVttTrip::where('hike_vtt_id', $hike->id)->delete();
        }
        HikeVtt::where('club_id', $club->id)->delete();
        User::where('club_id', $club->id)->update(['club_id' => null, 'is_club_admin' => false]);
        HikeVttImage::where('club_id', $club->id)->delete();
        Club::where('id', $club->id)->delete();

        return response()->json([
            'message' => 'club deleted'
        ], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/storeAvatar",
     *   summary="Club Avatar on storage",
     *   description="Ajoute un avatar d'un club au stockage du serveur",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *     @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *           @OA\Property(property="title", type="string", description="Type & Extension du fichier", example="images-jpg"),
     *           @OA\Property(
     *             property="image",
     *             type="file",
     *             description="Fichier de l'image",
     *             example="file:///data/user/0/com.kiroulouapp/cache/1074375c-759c-4789-b839-02520b4a74fb.jpg"
     *           ),
     *         ),
     *       ),
     *     ),
     *     @OA\Response(
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
    public function storeAvatar(Request $request, Club $club)
    {
        // On split l'ancienne image et l'extension de la nouvelle image
        $old_image_and_extension = explode("|", $request->title);

        // On check et delete l'ancienne image si elle existe
        if (Storage::exists($old_image_and_extension[0])) {
            Storage::delete($old_image_and_extension[0]);
        }

        // On renomme l'image avec l'extension passé dans le title
        $image_name = $club->id . '-' . rand(10000, 99999) . '-' . rand(100, 999) . '.' . $old_image_and_extension[1];

        // Emplacement de stockage de l'image
        $store = 'images/clubs/' . $club->id . '/avatars';

        $request->image->storeAs($store, $image_name);

        Club::where('id', $club->id)->update(['avatar' => $store . '/' . $image_name]);

        return response()->json(['message' => 'avatar uploaded'], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs?page={page}",
     *   summary="All clubs",
     *   description="Tous les clubs",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/page"),
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
        $clubs = Club::withCount('members')->withCount('userFollows')->withCount('posts')->paginate(10)->items();

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
     *       type="object",
     *       ref="#/components/schemas/ClubWithCounts"
     *     )
     *   )
     * )
     */
    public function clubInformations(Club $club)
    {
        // $club = Club::with('members')->with('organization')->with('posts')->with('userJoinRequests')->findOrFail($club->id);
        $club = Club::withCount('members')->withCount('userFollows')->withCount('posts')->findOrFail($club->id);
        return response()->json($club, 200);
    }

    /**
     * @OA\Put(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}",
     *   summary="Update Club",
     *   description="Modification d'un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClub"),
     *   @OA\Response(
     *     response=201,
     *     description="Club modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="club updated"
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
    public function updateClub(StoreClubRequest $request, Club $club)
    {
        $data = [
            'name' => $request->name,
            'address_id' => $request->address_id,
            'organization_id' => $request->organization_id,
            'short_name' => $request->short_name,
            'website' => $request->website
        ];

        Club::where('id', $club->id)->update($data);

        // On récupère toutes les infos du club pour le retourner
        $club = Club::findOrFail($club->id);

        return response()->json([
            "message" => "club updated",
            "club" => $club
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/clubMembers?page={page}",
     *   summary="Club's members",
     *   description="Le club et ses membres ainsi que les demandes d'adhésion",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ClubMembers")
     *   )
     * )
     */
    public function clubMembers(Club $club)
    {
        $club = Club::with('members')->with('userJoinRequests')->findOrFail($club->id);
        $members = $club->members->paginate(10)->items();

        $user_join_requests = $club->userJoinRequests;

        return response()->json([
            'members' => $members,
            'user_joint_requests' => $user_join_requests
        ], 200);
    }

    public function expelMember(Club $club, User $user)
    {
        User::where('id', $user->id)->update(['club_id' => null]);

        $club = Club::withCount('members')->withCount('userFollows')->withCount('posts')->findOrFail($club->id);

        $members = $this->clubMembers($club);

        return response()->json([
            'club' => $club,
            'members' => $members
        ], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/followOrUnfollow",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Follow/UnFollow club",
     *   description="Follow/UnFollow un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=202,
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
            return response()->json(["message" => 'Unfollow'], 202);
        } else {
            $request->user()->clubFollows()->attach($club->id);
        }

        return response()->json(["message" => 'Follow'], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/isClubFollowed",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Is user follow a club ?",
     *   description="Est-ce que le user follow le club ?",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="followed")
     *     )
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
    public function isClubFollowed(Request $request, Club $club)
    {
        $follow = DB::table('club_follows')
            ->where('user_id', $request->user()->id)
            ->where('club_id', $club->id)
            ->first();

        if (!$follow) {
            return response()->json(['message' => 'unfollow'], 404);
        }

        return response()->json(['message' => 'followed'], 200);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/requestToJoin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="User membership request",
     *   description="Demande d'adhésion au club",
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
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"user_id"},
     *       @OA\Property(property="user_id", type="number", example=1),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Accept la demande d'adhésion du user dans le club",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Accept Membership Request"),
     *       @OA\Property(
     *         property="join_requests",
     *         type="array",
     *         @OA\Items(
     *           @OA\Property(
     *             property="user_id",
     *             type="number",
     *             example=10005
     *           ),
     *           @OA\Property(
     *             property="club_id",
     *             type="number",
     *             example=1
     *           ),
     *           @OA\Property(
     *             property="created_at",
     *             type="string",
     *             example="2022-08-15 14:25:01"
     *           ),
     *           @OA\Property(
     *             property="updated_at",
     *             type="string",
     *             example=null
     *           ),
     *         )
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
    public function acceptRequestToJoin(StoreUserRequest $request, Club $club)
    {
        // On check que le user n'est pas déjà dans un club
        $is_already_in_club = User::where('id', $request->user_id)->where('club_id', '!=', NULL)->first();

        if ($is_already_in_club) {
            DB::table('club_join_requests')->where('user_id', $request->user_id)->delete();

            return response()->json(["message" => "L'utilisateur est déjà membre dans un club"], 409);
        }

        // On met à jour le user avec le club id
        User::where('id', $request->user_id)->update(['club_id' => $club->id]);

        // On supprime toutes les autres demande du user qui sont en attente dans la table club_join_requests
        DB::table('club_join_requests')->where('user_id', $request->user_id)->delete();

        // On récupère la table à jour
        $join_requests = DB::table('club_join_requests')->where('club_id', $club->id)->get();

        // On récupère le club à jour
        // $club = Club::find($club->id);

        return response()->json([
            "message" => "Accept Membership Request",
            "join_requests" => $join_requests
        ], 201);
    }

    /**
     * @OA\Delete(
     *   path="/clubs/{club_id}/denyRequestToJoin",
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   summary="Deny the user's membership request",
     *   description="Refus de la demande d'adhésion d'un user au club",
     *   tags={"Clubs"},
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"user_id"},
     *       @OA\Property(property="user_id", type="number", example=1),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Refuse la demande d'adhésion du user dans le club",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Deny Membership Request"),
     *       @OA\Property(
     *         property="join_requests",
     *         type="array",
     *         @OA\Items(
     *           @OA\Property(
     *             property="user_id",
     *             type="number",
     *             example=10005
     *           ),
     *           @OA\Property(
     *             property="club_id",
     *             type="number",
     *             example=1
     *           ),
     *           @OA\Property(
     *             property="created_at",
     *             type="string",
     *             example="2022-08-15 14:25:01"
     *           ),
     *           @OA\Property(
     *             property="updated_at",
     *             type="string",
     *             example=null
     *           ),
     *         )
     *       )
     *     )
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
    public function denyRequestToJoin(StoreUserRequest $request, Club $club)
    {
        // On supprime la demande du user qui est en attente dans la table club_join_requests pour le club
        DB::table('club_join_requests')->where('user_id', $request->user_id)->where('club_id', $club->id)->delete();

        // On récupère la table à jour
        $join_requests = DB::table('club_join_requests')->where('club_id', $club->id)->get();

        // On récupère le club à jour
        // $club = Club::find($club->id);

        return response()->json([
            "message" => "Deny Membership Request",
            "join_requests" => $join_requests
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
    public function showJoinRequests(Club $club)
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
     *     description="Admin changed",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="admin changed"),
     *       @OA\Property(
     *         property="user",
     *         ref="#/components/schemas/UserDetails"
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function changeAdmin(StoreUserRequest $request, Club $club)
    {
        $user = User::findOrFail($request->user_id);

        // On check que le futur admin est bien dans le club
        if ($user->club_id == $club->id) {
            // On retire l'admin au user connecté
            $request->user()->is_club_admin = false;
            $request->user()->save();

            // On met le user souhaité admin du club
            $user->is_club_admin = true;
            $user->save();

            $old_admin_updated = User::with("address")->findOrFail($request->user()->id);
        }

        return response()->json([
            'message' => 'admin changed',
            'user' => $old_admin_updated
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/profileImages",
     *   summary="Last 4 profile images",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubPostImage"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function profileImages(Club $club)
    {
        // On récupère les images des articles du club ainsi que les images des rando vtt
        $club_post_images = ClubPostImage::where('club_id', $club->id)->orderBy('created_at', 'DESC')->get();
        $club_hike_images = HikeVttImage::where('club_id', $club->id)->orderBy('created_at', 'DESC')->get();

        // On merge les 2 collections
        $images = collect($club_post_images)->merge($club_hike_images)->sortByDesc('created_at')->values();
        $images = $images->toArray();

        // On récupère les 4 premières images pour les afficher dans le profile du club
        $images = array_slice($images, 0, 4);

        return response()->json($images, 200);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/allImages?page={page}",
     *   summary="All club's images",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="current_page",
     *         type="number",
     *         example=1,
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ClubPostImage")
     *       ),
     *       @OA\Property(
     *         property="last_page",
     *         type="number",
     *         example=3,
     *       ),
     *       @OA\Property(
     *         property="total",
     *         type="number",
     *         example=11,
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function allImages(Club $club)
    {
        // On récupère les images des articles du club ainsi que les images des rando vtt
        $club_post_images = ClubPostImage::where('club_id', $club->id)->orderBy('created_at', 'DESC')->get();
        $club_hike_images = HikeVttImage::where('club_id', $club->id)->orderBy('created_at', 'DESC')->get();

        // On merge les 2 collections
        $images = collect($club_post_images)->merge($club_hike_images)->sortByDesc('created_at')->paginate(5)->values();

        return response()->json($images->toArray(), 200);
    }

    /**
     * @OA\Delete(
     *   path="/deleteImagePost/{image_id}",
     *   @OA\Parameter(ref="#/components/parameters/image_id"),
     *   summary="Delete a club post image",
     *   description="Supprime une image d'un article de club",
     *   tags={"Clubs"},
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
     *   ),
     * )
     */
    public function deleteImagePost(int $image_id)
    {
        ClubPostImage::where('id', $image_id)->delete();

        return response()->json(['message' => 'image deleted'], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/organizations",
     *   summary="list of organizations",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Organization")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function organizations()
    {
        $oragnizations = Organization::all();

        return response()->json($oragnizations, 200);
    }
}
