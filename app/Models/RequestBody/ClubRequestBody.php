<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddClub",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"name, address_id, organization_id"},
 *     @OA\Property(property="name", type="string", example="Mon super club"),
 *     @OA\Property(property="short_name", type="string", example="MSC", description="Diminutif du club"),
 *     @OA\Property(property="website", type="string", example="https://mon-super-club.com"),
 *     @OA\Property(property="address_id", type="number", example=1),
 *     @OA\Property(property="organization_id", type="number", example=2),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="AddClubPost",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"title, description"},
 *     @OA\Property(property="title", type="string", example="Mon super titre"),
 *     @OA\Property(property="description", type="string", example="Ma super description"),
 *     @OA\Property(property="hike_vtt_id", type="number", example=1),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="AddClubPostComment",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"message"},
 *     @OA\Property(property="message", type="string", example="Mon super commentaire"),
 *   )
 * )
 * 
 * @OA\RequestBody(
 *   request="ChangeAdmin",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"user_id"},
 *     @OA\Property(property="user_id", type="number", example=10005),
 *   )
 * )
 * 
 */
class ClubRequestBody
{
}
