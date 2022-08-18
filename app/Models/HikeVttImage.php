<?php

namespace App\Models;

use App\Models\Club;
use App\Models\HikeVtt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="HikeVttImage",
 *   description="Image d'une rando vtt d'un club",
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
 *     property="hike_vtt_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="club_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 */
class HikeVttImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'hike_vtt_id',
        'club_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function hikeVtt()
    {
        return $this->belongsTo(HikeVtt::class);
    }
}
