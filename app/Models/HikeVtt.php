<?php

namespace App\Models;

use App\Models\Club;
use App\Models\Address;
use App\Models\ClubPost;
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
 *     property="club_avatar",
 *         type="string",
 *     example="club-avatar.png"
 *   ),
 *   @OA\Property(
 *     property="department_code",
 *     type="string",
 *     example="29"
 *   ),
 *   @OA\Property(
 *     property="post",
 *     ref="#/components/schemas/ClubPostWithClub"
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="SearchHike",
 *   description="Rando VTT + Club + Address",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="Rando Côte Des Légendes VTT"
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     example="Ma super rando VTT ! Venez nombreux !"
 *   ),
 *   @OA\Property(
 *     property="public_price",
 *     type="string",
 *     example="6.00"
 *   ),
 *   @OA\Property(
 *     property="private_price",
 *     type="string",
 *     example="4.00"
 *   ),
 *   @OA\Property(
 *     property="address_id",
 *     type="number",
 *     example=1
 *   ),
 *   @OA\Property(
 *     property="flyer",
 *     type="string",
 *     example="images/clubs/1/hikes/6/flyer/6-90793-335.jpg"
 *   ),
 *   @OA\Property(
 *     property="date",
 *     type="string",
 *     example="2022-09-11"
 *   ),
 *   @OA\Property(
 *     property="club_id",
 *     type="number",
 *     example=1
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     example="2022-09-03 14:05:22"
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     example="2022-09-03 14:05:22"
 *   ),
 *   @OA\Property(
 *     property="deleted_at",
 *     type="string",
 *     example=null
 *   ),
 *   @OA\Property(
 *     property="id_club",
 *     type="number",
 *     example=1
 *   ),
 *   @OA\Property(
 *     property="club_name",
 *     type="string",
 *     example="Côte Des Légendes VTT"
 *   ),
 *   @OA\Property(
 *     property="short_name",
 *     type="string",
 *     example="CDL VTT"
 *   ),
 *   @OA\Property(
 *     property="avatar",
 *     type="string",
 *     example="images/clubs/1/avatars/1-29386-902.jpg"
 *   ),
 *   @OA\Property(
 *     property="id_address",
 *     type="number",
 *     example=3
 *   ),
 *   @OA\Property(
 *     property="street_address",
 *     type="string",
 *     example="Rue de la Marne"
 *   ),
 *   @OA\Property(
 *     property="region",
 *     type="string",
 *     example="Bretagne"
 *   ),
 *   @OA\Property(
 *     property="department",
 *     type="string",
 *     example="Finistère"
 *   ),
 *   @OA\Property(
 *     property="department_code",
 *     type="string",
 *     example="29"
 *   ),
 *   @OA\Property(
 *     property="city_id",
 *     type="number",
 *     example=3
 *   ),
 *   @OA\Property(
 *     property="zipcode_id",
 *     type="number",
 *     example=2
 *   ),
 *   @OA\Property(
 *     property="lat",
 *     type="string",
 *     example="48.568414"
 *   ),
 *   @OA\Property(
 *     property="lng",
 *     type="string",
 *     example="-4.31695"
 *   ),
 *   @OA\Property(
 *     property="distance",
 *     type="number",
 *     example=1.311950750397199
 *   ),
 *   @OA\Property(
 *     property="id_city",
 *     type="number",
 *     example=3
 *   ),
 *   @OA\Property(
 *     property="city",
 *     type="string",
 *     example="Lesneven"
 *   ),
 *   @OA\Property(
 *     property="id_zipcode",
 *     type="number",
 *     example=2
 *   ),
 *   @OA\Property(
 *     property="code",
 *     type="string",
 *     example="29260"
 *   ),
 *   @OA\Property(
 *     property="icon",
 *     type="string",
 *     example="<svg width=24 height=24 xmlns=http://www.w3.org/2000/svg><path d=M11.9 1a8.6 8.6 0 00-8.6 8.6c0 4.35 7.2 12.05 8.42 13.33a.24.24 0 00.35 0c1.22-1.27 8.42-9 8.42-13.33A8.6 8.6 0 0011.9 1zm0 11.67A3.07 3.07 0 1115 9.6a3.07 3.07 0 01-3.1 3.07z/></svg>",
 *     description="Icon pour mapMarker de la map Leaflet"
 *   ),
 *   @OA\Property(
 *     property="size",
 *     type="array",
 *     @OA\Items(
 *       type="number",
 *       example=24
 *     )
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
        'club_id',
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
        'club_avatar',
        'department_code',
        'icon',
        'size'
    ];

    protected $with = [
        'address',
        // 'post'
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

    public function post()
    {
        return $this->hasOne(ClubPost::class);
    }

    public function getDepartmentCodeAttribute()
    {
        return $this->address->department_code;
    }

    public function getClubNameAttribute()
    {
        return $this->club->name;
    }

    public function getClubAvatarAttribute()
    {
        return $this->club->avatar;
    }

    public function getIconAttribute()
    {
        return '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.9 1a8.6 8.6 0 00-8.6 8.6c0 4.35 7.2 12.05 8.42 13.33a.24.24 0 00.35 0c1.22-1.27 8.42-9 8.42-13.33A8.6 8.6 0 0011.9 1zm0 11.67A3.07 3.07 0 1115 9.6a3.07 3.07 0 01-3.1 3.07z"/></svg>';
    }

    public function getSizeAttribute()
    {
        return [24, 24];
    }
}
