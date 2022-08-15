<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddClubPost",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"title, description, club_id"},
 *     @OA\Property(property="title", type="string", example="Mon super titre"),
 *     @OA\Property(property="description", type="string", example="Ma super description"),
 *     @OA\Property(property="club_id", type="number", example=1),
 *   )
 * )
 */
class ClubPostRequestBody
{
}
