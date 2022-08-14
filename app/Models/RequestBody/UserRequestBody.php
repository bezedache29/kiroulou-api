<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="PostUser",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"email, password"},
 *     @OA\Property(property="email", type="string", example="simon-strueux@gmail.com"),
 *     @OA\Property(property="password", type="string", example="password12345"),
 *   )
 * )
 */
class UserRequestBody
{
}
