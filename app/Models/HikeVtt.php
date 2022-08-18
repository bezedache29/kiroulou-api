<?php

namespace App\Models;

use App\Models\Club;
use App\Models\Address;
use App\Models\HikeVttHype;
use App\Models\HikeVttTrip;
use App\Models\HikeVttImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="HikeVttSimple",
 *   description="Rando vtt simplifié",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="Rando CDL VTT",
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     example="Entre la vallée de la Flèche et la vallée de l'aber Vrac'h : 7 distances proposées de 19 à 56 km",
 *   ),
 *   @OA\Property(
 *     property="public_price",
 *     type="string",
 *     example="6",
 *     description="Prix public"
 *   ),
 *   @OA\Property(
 *     property="private_price",
 *     type="string",
 *     example="4",
 *     description="Prix licencié"
 *   ),
 *   @OA\Property(
 *     property="address",
 *     ref="#/components/schemas/Address"
 *   ),
 *   @OA\Property(
 *     property="flyer",
 *     type="string",
 *     example="flyer-cdlvtt-2022.png",
 *   ),
 *   @OA\Property(
 *     property="date",
 *     type="string",
 *     example="2022-08-15 14:25:01",
 *     description="Jour de la randonnée"
 *   ),
 *   @OA\Property(
 *     property="club_id",
 *     type="number",
 *     example=1,
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="HikeVttClub",
 *   description="Rando vtt avec club",
 *   allOf={@OA\Schema(ref="#/components/schemas/HikeVttSimple")},
 *   @OA\Property(
 *     property="club",
 *     ref="#/components/schemas/Club"
 *   ),
 *   @OA\Property(
 *     property="hike_vtt_images",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/HikeVttImage")
 *   ),
 *   @OA\Property(
 *     property="hike_vtt_hypes",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/HikeVttHype")
 *   ),
 *   @OA\Property(
 *     property="hike_vtt_trips",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/HikeVttTrip")
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="HikeVttAppends",
 *   description="Rando vtt avec club",
 *   allOf={@OA\Schema(ref="#/components/schemas/HikeVttSimple")},
 *   @OA\Property(
 *     property="club_name",
 *     type="string",
 *     example="Côte Des Légendes VTT"
 *   ),
 *   @OA\Property(
 *     property="department_code",
 *     type="string",
 *     example="29"
 *   ),
 * )
 */
class HikeVtt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'public_price',
        'private_price',
        'address_id',
        'flyer',
        'date',
        'club_id'
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'club',
    ];

    protected $appends = [
        'club_name',
        'department_name'
    ];

    protected $with = [
        'address'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function hikeVttHypes()
    {
        return $this->hasMany(HikeVttHype::class);
    }

    public function hikeVttImages()
    {
        return $this->hasMany(HikeVttImage::class);
    }

    public function hikeVttTrips()
    {
        return $this->hasMany(HikeVttTrip::class);
    }

    public function getDepartmentNameAttribute()
    {
        return $this->address->department_code;
    }

    public function getClubNameAttribute()
    {
        return $this->club->name;
    }
}
