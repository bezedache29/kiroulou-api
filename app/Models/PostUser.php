<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostUserLike;
use App\Models\PostUserImage;
use App\Models\PostUserComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @OA\Schema(
 *   schema="PostUserSimple",
 *   description="Article d'un user simple avec les compteurs",
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     example="b7d15d52-0458-4a39-a59e-a7a370e7f31c",
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
 *     property="user_id",
 *     type="number",
 *     example=10005,
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
 *     property="deleted_at",
 *     type="string",
 *     example=null,
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserWithUser",
 *   description="Article d'un user avec les details du user",
 *   allOf={@OA\Schema(ref="#/components/schemas/PostUserSimple")},
 *   @OA\Property(
 *     property="user",
 *     ref="#/components/schemas/UserDetails"
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserCounts",
 *   description="Article d'un user avec les compteurs de commentaires et likes",
 *   allOf={@OA\Schema(ref="#/components/schemas/PostUserSimple")},
 *   @OA\Property(
 *     property="post_user_likes_count",
 *     type="number",
 *     example=12
 *   ),
 *   @OA\Property(
 *     property="post_user_comments_count",
 *     type="number",
 *     example=3
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserImages",
 *   description="Article d'un user avec les images",
 *   allOf={@OA\Schema(ref="#/components/schemas/PostUserSimple")},
 *     @OA\Property(
 *     property="post_user_images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserImage")
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserCountsAndImages",
 *   description="Article d'un user avec les compteurs likes et commentaires et les images",
 *   allOf={@OA\Schema(ref="#/components/schemas/PostUserCounts")},
 *     @OA\Property(
 *     property="post_user_images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserImage")
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserIsUse",
 *   description="A quoi il sert ?",
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
 *     property="user_id",
 *     type="number",
 *     example=10005,
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
 *     property="post_user_images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserImage")
 *   ),
 *   @OA\Property(
 *     property="post_user_likes",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserLike")
 *   ),
 *   @OA\Property(
 *     property="post_user_comments",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserComment")
 *   ),
 * )
 */
class PostUser extends Model
{
    use HasFactory, SoftDeletes, \App\Http\Traits\UsesUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    protected $with = [
        // 'postUserComments',
        // 'postUserLikes',
        // 'images'
    ];

    protected $hidden = [
        // 'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postUserComments()
    {
        return $this->hasMany(PostUserComment::class);
    }

    public function postUserLikes()
    {
        return $this->hasMany(PostUserLike::class);
    }

    public function images()
    {
        return $this->hasMany(PostUserImage::class);
    }
}
