<?php

namespace App\Http\Controllers;

use App\Models\HikeVtt;
use App\Models\HikeVttHype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string'],
                'description' => ['required', 'string'],
                'public_price' => ['required', 'string'],
                'private_price' => ['string'],
                'date' => ['required', 'date'],
                'club_id' => ['required', 'exists:clubs,id'],
                'address_id' => ['required', 'exists:addresses,id'],
            ],
            [
                'name.required' => 'Le nom est obligatoire',
                'name.string' => 'Le nom doit être une chaine de caractères',
                'description.required' => 'La description est obligatoire',
                'description.string' => 'La description doit être une chaine de caractères',
                'public_price.required' => 'Le prix public est obligatoire',
                'public_price.string' => 'Le prix public doit être une chaine de caractères',
                'private_price.string' => 'Le prix licencié doit être une chaine de caractères',
                'date.required' => 'La date est obligatoire',
                'date.date' => 'La date doit être une date valide',
                'club_id.required' => 'L\'id du club est obligatoire',
                'address_id.required' => 'L\'id de l\'adresse est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->flyer) {
            // !TODO : Ajout flyer en storage

            $flyer = 'flyer-name.png';
            $data['flyer'] = $flyer;
        }

        $hike = HikeVtt::create($data);

        return response()->json([
            'message' => 'hike created',
            'hike_vtt_id' => $hike->id
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Hikes VTT"},
     *   path="/hikes/vtt/{hike_id}/show",
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
    public function show(Int $hike_id)
    {
        // Permet de rendre le club visible alors qu'il est dans le hidden de HikeVtt
        $hike_vtt = HikeVtt::with('hikeVttImages')->with('hikeVttHypes')->with('hikeVttTrips')->findOrFail($hike_id)->makeVisible('club');

        return response()->json($hike_vtt, 200);
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
     *         property="post",
     *         ref="#/components/schemas/HikeVttSimple"
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
            $message = 'unhyped';
        } else {
            HikeVttHype::create([
                'user_id' => $request->user()->id,
                'hike_vtt_id' => $hike_id
            ]);
            $message = 'hyped';
        }

        $hike_vtt = HikeVtt::withCount('hikeVttHypes')->findOrFail($hike_id);

        return response()->json([
            'message' => $message,
            'hike_vtt' => $hike_vtt,
        ], 201);
    }
}
