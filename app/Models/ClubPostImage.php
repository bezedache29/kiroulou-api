<?php

namespace App\Models;

use App\Models\ClubPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="ClubPostImage",
 *   description="Image d'un post de club",
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
 *     property="club_post_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 */
class ClubPostImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'club_post_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function clubPost()
    {
        return $this->belongsTo(ClubPost::class);
    }
}
