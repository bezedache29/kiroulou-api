<?php

namespace App\Models;

use App\Models\User;
use App\Models\HikeVtt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="HikeVttHype",
 *   description="Les users hypes par la rando vtt",
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="hike_vtt_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 */
class HikeVttHype extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'hike_vtt_id'
    ];

    protected $with = [
        //
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'hike_vtt_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hikeVtt()
    {
        return $this->belongsTo(HikeVtt::class);
    }
}
