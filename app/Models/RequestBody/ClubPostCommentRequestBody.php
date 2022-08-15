<?php

namespace App\Models\RequestBody;

/**
 * @OA\RequestBody(
 *   request="AddClubPostComment",
 *   required=true,
 *   @OA\JsonContent(
 *     required={"message, user_id, club_post_id"},
 *     @OA\Property(property="user_id", type="number", example=10005),
 *     @OA\Property(property="club_post_id", type="number", example=1),
 *     @OA\Property(property="message", type="string", example="Mon super commentaire"),
 *   )
 * )
 */
class ClubPostCommentRequestBody
{
}
