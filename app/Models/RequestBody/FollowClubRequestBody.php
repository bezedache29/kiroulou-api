<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="FollowClub",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"user_id, club_id"},
 *     @OA\Property(property="user_id", type="number", example="1005"),
 *     @OA\Property(property="club_id", type="number", example="2"),
 *   )
 * )
 */
class FollowClubRequestBody
{
}
