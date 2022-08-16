<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddBike",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"name, brand, model, bike_type_id, date"},
 *     @OA\Property(property="name", type="string", example="Mon Super VTT"),
 *     @OA\Property(property="brand", type="string", example="Canyon"),
 *     @OA\Property(property="model", type="string", example="Lux world cup cfr"),
 *     @OA\Property(property="bike_type_id", type="number", example=1),
 *     @OA\Property(property="date", type="string", example="2022-08-15 14:25:01"),
 *     @OA\Property(property="weight", type="string", example="9.8"),
 *     @OA\Property(property="image", type="string", example="canyon.png"),
 *   )
 * )
 */
class BikeRequestBody
{
}
