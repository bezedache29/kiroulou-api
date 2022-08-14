<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Club;
use App\Models\Address;
use App\Models\ClubFollow;
use App\Models\ClubMember;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClubController extends Controller
{

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs",
     *   summary="Tous les clubs",
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
     *       @OA\Items(ref="#/components/schemas/Club")
     *     )
     *   )
     * )
     */
    public function index()
    {
        // $clubs = Club::paginate(1);
        $clubs = Club::get();

        return response()->json($clubs, 200);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs",
     *   summary="Ajout Club",
     *   description="Création d'un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClub"),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
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
    public function store(Request $request)
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
        $is_on_club = ClubMember::where('user_id', $request->user()->id)->where('deleted_at', null)->first();

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

        // Ici ajouter l'admin du club
        ClubMember::create([
            'user_id' => $request->user()->id,
            'club_id' => $club_created->id,
            'is_user_admin' => true
        ]);

        return response()->json(["message" => "club created"], 201);
    }

    public function follow(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'club_id' => ['required'],
            ],
            [
                'club_id.required' => 'Le club_id est obligatoire',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // On rempli la table pivot des users qui follow le club en ajoutant le club_id avec le user connecté
        $request->user()->clubFollows()->attach($request->club_id);

        return response()->json(["message" => "Follow"], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
