<?php

namespace App\Models;

/**
 * @OA\RequestBody(
 *   request="PostUser",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"email, password"},
 *     @OA\Property(property="firstname", type="string", example="Simon"),
 *     @OA\Property(property="lastname", type="string", example="Strueux"),
 *     @OA\Property(property="email", type="string", example="simon-strueux@gmail.com"),
 *     @OA\Property(property="password", type="string", example="password12345"),
 *     @OA\Property(property="lat", type="number", format="float", example="48.1478596"),
 *     @OA\Property(property="lng", type="number", format="float", example="-4.1458755"),
 *   )
 * )
 */
class UserRequestBody
{
}
