<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddAddress",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"street_address, lat, lng, region, department, department_code"},
 *     @OA\Property(property="street_address", type="string", example="Ma super rue"),
 *     @OA\Property(property="lat", type="string", example="48.5740185", description="Lattitude GPS"),
 *     @OA\Property(property="lng", type="string", example="-4.3335965", description="Longitude GPS"),
 *     @OA\Property(property="region", type="string", example="Bretagne"),
 *     @OA\Property(property="department", type="string", example="Finistère"),
 *     @OA\Property(property="department_code", type="string", example="29"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="NewAddress",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"street_address, lat, lng, region, department, department_code, city, zipcode"},
 *     @OA\Property(property="street_address", type="string", example="Ma super rue"),
 *     @OA\Property(property="lat", type="string", example="48.5740185", description="Lattitude GPS"),
 *     @OA\Property(property="lng", type="string", example="-4.3335965", description="Longitude GPS"),
 *     @OA\Property(property="region", type="string", example="Bretagne"),
 *     @OA\Property(property="department", type="string", example="Finistère"),
 *     @OA\Property(property="department_code", type="string", example="29"),
 *     @OA\Property(property="city", type="string", example="Lesneven"),
 *     @OA\Property(property="zipcode", type="string", example="29260"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="CheckAddress",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"street_address, city, zipcode"},
 *     @OA\Property(property="street_address", type="string", example="Ma super rue"),
 *     @OA\Property(property="city", type="string", example="Lesneven"),
 *     @OA\Property(property="zipcode", type="string", example="29890"),
 *   )
 * )
 */
class AddressRequestBody
{
}
