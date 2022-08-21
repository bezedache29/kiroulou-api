<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddHikeVtt",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"name, description, public_price, address_id, date, club_id"},
 *     @OA\Property(property="name", type="string", example="Ma super Rando VTT"),
 *     @OA\Property(property="description", type="string", example="Ma super description de Rando VTT"),
 *     @OA\Property(property="public_price", type="string", example="6"),
 *     @OA\Property(property="private_price", type="string", example="4"),
 *     @OA\Property(property="date", type="string", example="2023-02-20"),
 *     @OA\Property(property="street_address", type="string", example="Ma super rue"),
 *     @OA\Property(property="city", type="string", example="Nantes"),
 *     @OA\Property(property="zipcode", type="string", example="44000"),
 *     @OA\Property(property="lat", type="string", example="48.5740185", description="Lattitude GPS"),
 *     @OA\Property(property="lng", type="string", example="-4.3335965", description="Longitude GPS"),
 *     @OA\Property(property="region", type="string", example="Bretagne"),
 *     @OA\Property(property="department", type="string", example="Finistère"),
 *     @OA\Property(property="department_code", type="string", example="29"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="HikeVttDepartment",
 *   required=true,
 *   @OA\JsonContent(
 *     @OA\Property(property="department_code", type="string", example="29"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="HikeVttDepartmentMonth",
 *   required=true,
 *   @OA\JsonContent(
 *     @OA\Property(property="year", type="string", example="2022"),
 *     @OA\Property(property="month", type="string", example="8"),
 *     @OA\Property(property="department_code", type="string", example="29"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="HikeVttAddTrip",
 *   required=true,
 *   @OA\JsonContent(
 *     @OA\Property(property="distance", type="string", example="35"),
 *     @OA\Property(property="height_difference", type="string", example="280", description="dénivelé positif"),
 *     @OA\Property(property="difficulty", type="number", example=1),
 *     @OA\Property(property="supplies", type="string", example=1, description="Nombre de ravitaillements sur le parcours"),
 *   )
 * )
 */
class HikeVttRequestBody
{
}
