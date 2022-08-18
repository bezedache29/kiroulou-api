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
 *     @OA\Property(property="address_id", type="number", example=1),
 *     @OA\Property(property="date", type="string", example="2023-02-20"),
 *     @OA\Property(property="club_id", type="number", example=1),
 *   )
 * )
 */
class HikeVttRequestBody
{
}
