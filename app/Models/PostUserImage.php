<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="PostUserImage",
 *   description="Image d'un post d'un user",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="image",
 *     type="string",
 *     example="mon-image.png",
 *   ),
 *   @OA\Property(
 *     property="user_post_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 */
class PostUserImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'post_user_id',
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
