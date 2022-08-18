<?php

namespace App\Models;

use App\Models\Club;
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
 *     type="string",
 *     example="e8b90907-aee5-473d-962b-277de7be83fb",
 *   ),
 *   @OA\Property(
 *     property="club_id",
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
        'club_post_id',
        'club_id'
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

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
