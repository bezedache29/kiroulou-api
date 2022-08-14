<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddClub",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"name, street_address, city, zipcode, lat, lng, region, department, department_code, organization"},
 *     @OA\Property(property="name", type="string", example="Mon super club"),
 *     @OA\Property(property="shortName", type="string", example="MSC", description="Diminutif du club"),
 *     @OA\Property(property="website", type="string", example="https://mon-super-club.com"),
 *     @OA\Property(property="avatar", type="string", example="1.png", description="Renommage de l'avatar avec id du club"),
 *     @OA\Property(property="organization", type="number", example="3", description="organization_id du type de club"),
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
 */
class ClubRequestBody
{
}
