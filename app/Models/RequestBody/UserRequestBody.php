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
 * 
 * @OA\RequestBody(
 *   request="AddPostUser",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"title, description, user_id"},
 *     @OA\Property(property="title", type="string", example="Mon super titre"),
 *     @OA\Property(property="description", type="string", example="Ma super description"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="AddPostUserComment",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"message", "post_user_id", "user_id"},
 *     @OA\Property(property="message", type="string", example="Mon super commentaire"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="UpdateUser",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"email", "user_id", "address_id"},
 *     @OA\Property(property="email", type="string", example="simon-strueux@gmail.com"),
 *     @OA\Property(property="firstname", type="string", example="Simon"),
 *     @OA\Property(property="lastname", type="string", example="Strueux"),
 *     @OA\Property(property="user_id", type="number", example=10005),
 *     @OA\Property(property="address_id", type="number", example=1),
 *   )
 * )
 */
class UserRequestBody
{
}
