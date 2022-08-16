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
 *   schema="PostUser",
 *   description="Article d'un user",
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
 *     property="Post_user_likes",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/PostUserLike")
 *   ),
 * )
 */
class PostUser extends Model
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
        'user_id'
    ];

    protected $with = [
        // 'postUserImages',
        // 'postUserComments',
        'postUserLikes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function postUserImages()
    // {
    //     return $this->hasMany(PostUserImage::class);
    // }

    public function postUserComments()
    {
        return $this->hasMany(PostUserComment::class);
    }

    public function postUserLikes()
    {
        return $this->hasMany(PostUserLike::class);
    }
}
