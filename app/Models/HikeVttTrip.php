<?php

namespace App\Models;

use App\Models\HikeVtt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="HikeVttTrip",
 *   description="Parcours d'une rando vtt d'un club",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="distance",
 *     type="string",
 *     example="54",
 *   ),
 *   @OA\Property(
 *     property="height_difference",
 *     type="string",
 *     example="700",
 *     description="Dénivelé positif en metre"
 *   ),
 *   @OA\Property(
 *     property="difficulty",
 *     type="number",
 *     example=4,
 *     description="Type de difficulté 1-facile 2-moyen 3-sportif 4-sportif+"
 *   ),
 *   @OA\Property(
 *     property="supplies",
 *     type="number",
 *     example=2,
 *     description="Nombre de ravitaillements sur le parcours"
 *   ),
 *   @OA\Property(
 *     property="hike_vtt_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 */
class HikeVttTrip extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'distance',
        'height_difference',
        'difficulty',
        'supplies',
        'hike_vtt_id',
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function hikeVtt()
    {
        return $this->belongsTo(HikeVtt::class);
    }
}
