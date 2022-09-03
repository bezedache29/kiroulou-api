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
 *     @OA\Property(property="address_id", type="number", example=1),
 *     @OA\Property(property="club_id", type="number", example=2),
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
 *     @OA\Property(property="difficulty", type="string", example="1"),
 *     @OA\Property(property="supplies", type="string", example=1, description="Nombre de ravitaillements sur le parcours"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="SearchHikes",
 *   required=true,
 *   @OA\JsonContent(
 *     @OA\Property(property="lat", type="string", example="48.568414"),
 *     @OA\Property(property="lng", type="string", example="-4.31695"),
 *     @OA\Property(property="distance", type="number", example=12),
 *   )
 * )
 */
class HikeVttRequestBody
{
}
