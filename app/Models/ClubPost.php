<?php

namespace App\Models;

use App\Models\Club;
use App\Models\ClubPostLike;
use App\Models\ClubPostImage;
use App\Models\ClubPostComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 * @OA\Schema(
 *   schema="ClubPostSimple",
 *   description="Article d'un club",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="title",
 *     type="string",
 *     example="Mon super titre",
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     example="Ma super description d'article",
 *   ),
 *   @OA\Property(
 *     property="club_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     example="2022-08-15 14:25:01",
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     example=null,
 *   ),
 *   @OA\Property(
 *     property="images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/ClubPostImage")
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="ClubPostCounts",
 *   description="Article d'un club avec count likes et commentaires",
 *   allOf={@OA\Schema(ref="#/components/schemas/ClubPostSimple")},
 *   @OA\Property(
 *     property="post_likes_count",
 *     type="number",
 *     example=36
 *   ),
 *   @OA\Property(
 *     property="post_comments_count",
 *     type="number",
 *     example=27
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="ClubPostLikes",
 *   description="Article d'un club avec count likes",
 *   allOf={@OA\Schema(ref="#/components/schemas/ClubPostSimple")},
 *   @OA\Property(
 *     property="post_likes_count",
 *     type="number",
 *     example=36
 *   ),
 * )
 */
class ClubPost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'club_id'
    ];

    protected $with = [
        'images',
        // 'postlikes',
        // 'comments'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function images()
    {
        return $this->hasMany(ClubPostImage::class);
    }

    public function comments()
    {
        return $this->hasMany(ClubPostComment::class);
    }

    public function postlikes()
    {
        return $this->hasMany(ClubPostLike::class);
    }
}
