<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="PostUserCommentCount",
 *   description="ID du user qui a commenté l'article",
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=10005,
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="PostUserComment",
 *   description="Commentaire d'un article d'un user par un user",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=10005,
 *   ),
 *   @OA\Property(
 *     property="post_user_id",
 *     type="string",
 *     example="0d2991f8-132b-43fc-87f9-9bbcbf0d7877",
 *   ),
 *   @OA\Property(
 *     property="message",
 *     type="string",
 *     example="Mon super commentaire d'un article d'un user",
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
 *   @OA\Property(
 *     property="user",
 *     ref="#/components/schemas/UserDetails"
 *   ),
 * )
 */
class PostUserComment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'post_user_id',
        'user_id'
    ];

    protected $with = [
        'user'
    ];

    public function post()
    {
        return $this->belongsTo(PostUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
