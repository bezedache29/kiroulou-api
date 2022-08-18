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
 *   description="Les images d'un post d'un user",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="post_user_id",
 *     type="string",
 *     example="0c752632-4e22-4c0c-839e-c50409904049",
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=10005,
 *   ),
 *   @OA\Property(
 *     property="image",
 *     type="string",
 *     example="image.png",
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
        'post_user_id',
        'user_id',
        'image'
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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
