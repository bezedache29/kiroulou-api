<?php

namespace App\Models;

use App\Models\User;
use App\Models\ClubPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="ClubPostLike",
 *   description="Id du user qui a liké l'article du club",
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="ClubPostLikeDetails",
 *   description="Like ou Unlike d'un post d'un club",
 *   allOf={@OA\Schema(ref="#/components/schemas/ClubPostLike")},
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="club_post_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="user",
 *     ref="#/components/schemas/UserDetails"
 *   ),
 * )
 */
class ClubPostLike extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'club_post_id'
    ];

    protected $with = [
        // 'user',
        // 'userPost'
    ];

    protected $hidden = [
        'id',
        'club_post_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clubPost()
    {
        return $this->belongsTo(ClubPost::class);
    }
}
