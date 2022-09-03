<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\HikeVtt;
use App\Models\ClubPost;
use App\Models\HikeVttHype;
use App\Models\HikeVttTrip;
use App\Models\HikeVttImage;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreHikeVttRequest;
use App\Http\Requests\StoreHikeVttDateRequest;

class HikeVttController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt",
     *   summary="All vtt hikes",
     *   description="Toutes les randonnées VTT",
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/HikeVttSimple")
     *     )
     *   )
     * )
     */
    public function index()
    {
        $hikes = HikeVtt::get();

        return response()->json($hikes, 200);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt",
     *   summary="Add Hike Vtt",
     *   description="Création d'une randonnéee vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddHikeVtt"),
     *   @OA\Response(
     *     response=201,
     *     description="Club créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Hike vtt created"
     *       ),
     *       @OA\Property(
     *         property="hike_vtt_id",
     *         type="number",
     *         example=1
     *       ),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function store(StoreHikeVttRequest $request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'public_price' => $request->public_price,
            'private_price' => $request->private_price,
            'date' => $request->date,
            'address_id' => $request->address_id,
            'club_id' => $request->club_id
        ];

        $hike = HikeVtt::create($data);

        return response()->json([
            'message' => 'hike created',
            'hike_vtt_id' => $hike->id
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}",
     *   summary="Hike vtt details",
     *   description="Détails de la randonnée vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/HikeVttClub")
     *     )
     *   )
     * )
     */
    public function show(int $hike_id)
    {
        // Permet de rendre le club visible alors qu'il est dans le hidden de HikeVtt
        $hike_vtt = HikeVtt::with('hikeVttImages')->with('post')->withCount('hikeVttHypes')->with('hikeVttHypes')->with('hikeVttTrips')->findOrFail($hike_id)->makeVisible('club');

        return response()->json($hike_vtt, 200);
    }

    /**
     * @OA\Put(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}",
     *   summary="Update Hike Vtt",
     *   description="Modification d'une randonnéee vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddHikeVtt"),
     *   @OA\Response(
     *     response=201,
     *     description="Club modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Hike vtt updated"
     *       ),
     *       @OA\Property(
     *         property="hike_vtt_id",
     *         type="number",
     *         example=1
     *       ),
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
    public function update(StoreHikeVttRequest $request, Int $hike_id)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'public_price' => $request->public_price,
            'private_price' => $request->private_price,
            'date' => $request->date,
            'address_id' => $request->address_id,
            'club_id' => $request->club_id,
        ];

        HikeVtt::where('id', $hike_id)->update($data);

        return response()->json([
            'message' => 'hike updated',
            'hike_vtt_id' => $hike_id
        ], 201);
    }

    /**
     * @OA\Put(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/changeDate",
     *   summary="Update date Hike Vtt",
     *   description="Modification de la date d'une randonnéee vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       type="string",
     *       required={"date"},
     *       @OA\Property(property="date", type="string", example="2022-12-20")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Club modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Hike vtt updated"
     *       ),
     *       @OA\Property(
     *         property="hike_vtt_id",
     *         type="number",
     *         example=1
     *       ),
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
    public function changeDate(StoreHikeVttDateRequest $request, Int $hike_id)
    {
        $hike = HikeVtt::findOrFail($hike_id);
        $hike->update($request->all());

        return response()->json([
            'message' => 'hike updated',
            'hike_vtt_id' => $hike->id
        ], 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}",
     *   summary="Delete Hike Vtt",
     *   description="Suppression d'une randonnéee vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
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
    public function delete(Int $hike_id)
    {
        $hike = HikeVtt::findOrFail($hike_id);

        $post = ClubPost::where('hike_vtt_id', $hike_id)->where('id', $hike->post->id)->first();

        $format_date = Carbon::create($hike->date)->locale('fr_FR');
        $day_text = $format_date->getTranslatedDayName('dddd');
        $day_number = $format_date->day;
        $day_month = $format_date->getTranslatedMonthName('MMMM');
        $day_year = $format_date->year;
        $date = $day_text . ' ' . $day_number . ' ' . $day_month . ' ' . $day_year;

        $data = [
            'title' => "Rando Annulé !",
            'description' => "La randonnée VTT " . $post->title . " qui devait se dérouler le " . $date . ", doit malheuresement être annulée.",
            'hike_vtt_id' => $hike_id,
            'cancelled' => true,
            'club_id' => $hike->club_id
        ];

        $new_post = ClubPost::create($data);

        $post->delete();
        $post->save();
        $hike->delete();
        $hike->save();

        return response()->json([
            'message' => 'hike deleted',
            'post' => $new_post
        ], 201);

        // Uniquement du soft delete pour le moment
        HikeVtt::where('id', $hike_id)->delete();

        // TODO Update le post pour annoncer le cancel
        // Suppression de l'ancien post
        // Création d'un nouveau post pour annoncer le cancel

        return response()->json(['message' => 'hike deleted'], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/storeImage",
     *   summary="Flyer or images to storage",
     *   description="Ajoute un flyer ou des images au stockage du serveur",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *     @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(
     *           @OA\Property(property="title", type="string", description="Type & Extension du fichier", example="images-jpg"),
     *           @OA\Property(
     *             property="flyer",
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
    public function storeImage(Request $request, Int $hike_id)
    {
        // 1 - Flyer
        // 2 - Extension
        // 3 - OldFlyer || create

        // On explode le title request pour récupérer l'extension du fichier et le type d'image a uploader (flyer ou images)
        $title = explode("|", $request->title);

        if ($title[2] !== 'create') {
            // Suppression
            if (Storage::exists($title[2])) {
                Storage::delete($title[2]);
            }
        }

        // On renomme l'image avce l'extension passé dans le title
        $image_name = $hike_id . '-' . rand(10000, 99999) . '-' . rand(100, 999) . '.' . $title[1];

        // Emplacement de stockage de l'image
        $store = 'images/clubs/' . $request->user()->club_id . '/hikes/' . $hike_id . '/' . $title[0];

        // Suivant le type on stock l'image ou le flyer
        // On ajoute dans la DB image ou on update la hikevtt
        if ($title[0] == 'image') {
            // On store l'image a l'endroit voulu, avec son nouveau nom
            $request->image->storeAs($store, $image_name);

            HikeVttImage::create([
                'image' => $store . '/' . $image_name,
                'hike_vtt_id' => $hike_id,
                'club_id' => $request->user()->club_id
            ]);
        } else {
            // On store l'image a l'endroit voulu, avec son nouveau nom
            $request->flyer->storeAs($store, $image_name);

            HikeVtt::where('id', $hike_id)->update([
                'flyer' => $store . '/' . $image_name
            ]);
        }

        return response()->json('OK', 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/deleteImages",
     *   summary="Delete hike's vtt images",
     *   description="Supprime les images d'une randonnée vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
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
    public function deleteImages(int $hike_id)
    {
        $images = HikeVttImage::where('hike_vtt_id', $hike_id)->get();

        foreach ($images as $image) {
            if (Storage::exists($image['image'])) {
                Storage::delete($image['image']);
            }
        }

        return response()->json([
            'message' => 'images deleted'
        ], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/hypeOrUnhype",
     *   summary="Hype or Unhype Hike Vtt",
     *   description="S'interesser ou ne plus s'intéresser a une rando vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\Response(
     *     response=201,
     *     description="Hype ou Unhype effectué",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Hyped"
     *       ),
     *       @OA\Property(
     *         property="hike_vtt",
     *         ref="#/components/schemas/HikeVttClub"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function hypeOrUnhype(Request $request, Int $hike_id)
    {
        // Check si le post a été like par le user
        $is_hyped = HikeVttHype::where('user_id', $request->user()->id)->where('hike_vtt_id', $hike_id)->first();

        if ($is_hyped) {
            $is_hyped->delete();

            $hike_vtt = HikeVtt::with('hikeVttImages')
                ->withCount('hikeVttHypes')
                ->with('hikeVttHypes')
                ->with('hikeVttTrips')
                ->findOrFail($hike_id)
                ->makeVisible('club');

            return response()->json([
                'message' => 'unhype',
                'hike_vtt' => $hike_vtt,
            ], 202);
        }

        HikeVttHype::create([
            'user_id' => $request->user()->id,
            'hike_vtt_id' => $hike_id
        ]);

        $hike_vtt = HikeVtt::with('hikeVttImages')
            ->withCount('hikeVttHypes')
            ->with('hikeVttHypes')
            ->with('hikeVttTrips')
            ->findOrFail($hike_id)
            ->makeVisible('club');

        return response()->json([
            'message' => 'hyped',
            'hike_vtt' => $hike_vtt,
        ], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/searchInDepartment",
     *   summary="Search hikes in the department",
     *   description="Recherche les randos vtt dans le département",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/HikeVttDepartment"),
     *   @OA\Response(
     *     response=201,
     *     description="Données récupérées",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/HikeVttAppends")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function searchInDepartment(Request $request)
    {
        // On récupère les rando vtt futur
        $hikes = HikeVtt::where('date', '>=', Carbon::now())->orderBy('date', 'ASC')->get();

        // On récupère les rando du départment voulu
        $hikes = $hikes->where('department_name', $request->department_code)->values();

        return response()->json($hikes->toArray(), 200);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/searchInMonth",
     *   summary="Search for a hike in the departament a specific month",
     *   description="Rechercher une randonnée dans le departament un mois précis",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/HikeVttDepartmentMonth"),
     *   @OA\Response(
     *     response=201,
     *     description="Données récupérées",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/HikeVttAppends")
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function searchInMonth(Request $request)
    {
        $date = Carbon::createFromDate($request->year, $request->month, '01');

        $hikes = HikeVtt::whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->get();

        $hikes = $hikes->where('department_name', $request->department_code)->values();

        return response()->json($hikes->toArray(), 200);
    }

    /**
     * @OA\Post(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/trip",
     *   summary="Store hike's vtt trip",
     *   description="Ajout un circuit a une randonnée vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/HikeVttAddTrip"),
     *     @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function storeTrip(StoreTripRequest $request, int $hike_id)
    {
        $data = $request->all();
        $data['hike_vtt_id'] = $hike_id;
        HikeVttTrip::create($data);

        return response()->json(['message' => 'trip created'], 201);
    }

    /**
     * @OA\Put(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/trip/{trip_id}",
     *   summary="Update hike's vtt trip",
     *   description="Modifie un circuit a une randonnée vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\Parameter(ref="#/components/parameters/trip_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/HikeVttAddTrip"),
     *     @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function updateTrip(StoreTripRequest $request, int $hike_id, int $trip_id)
    {
        $data = $request->all();
        HikeVttTrip::where('id', $trip_id)->update($data);

        return response()->json(['message' => 'trip updated'], 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/trip/{trip_id}",
     *   summary="Delete hike's vtt trip",
     *   description="Supprime un circuit a une randonnée vtt",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/hike_id"),
     *   @OA\Parameter(ref="#/components/parameters/trip_id"),
     *     @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
     *   )
     * )
     */
    public function deleteTrip(int $hike_id, int $trip_id)
    {
        HikeVttTrip::where('id', $trip_id)->delete();

        return response()->json(['message' => 'trip deleted'], 201);
    }
}
