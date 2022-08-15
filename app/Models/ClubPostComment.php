<?php

namespace App\Models;

use App\Models\User;
use App\Models\ClubPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="ClubPostComment",
 *   description="Commentaire d'un article d'un club par un user",
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
 *     property="club_post_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="message",
 *     type="string",
 *     example="Mon super commentaire d'un article du club",
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
 * )
 */
class ClubPostComment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_post_id',
        'user_id',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(ClubPost::class);
    }
}
