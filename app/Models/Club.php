<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Address;
use App\Models\HikeVtt;
use App\Models\ClubPost;
use App\Models\ClubFollow;
use App\Models\ClubMember;
use App\Models\HikeVttImage;
use App\Models\Organization;
use App\Models\ClubPostImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Club",
 *   description="Club",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example="1",
 *     description="Id du user",
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="Côte des légendes VTT",
 *     description="Nom du club",
 *   ),
 *   @OA\Property(
 *     property="short_name",
 *     type="string",
 *     example="CDL VTT",
 *     description="Nom raccourci du club",
 *   ),
 *   @OA\Property(
 *     property="website",
 *     type="string",
 *     example="http://cotedeslegendesvtt.free.fr/",
 *     description="Site internet du club",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="avatar",
 *     type="string",
 *     example="1.png",
 *     description="Nom de l'avatar (id du club + .png)",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="next_hike",
 *     description="Prochaine randonnée du club",
 *     ref="#/components/schemas/HikeVttAppends"
 *   ),
 *   @OA\Property(
 *     property="address",
 *     description="Adresse du club",
 *     ref="#/components/schemas/Address"
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="ClubWithCounts",
 *   description="Club avec le nombre de follows, articles, membres",
 *   allOf={@OA\Schema(ref="#/components/schemas/Club")},
 *   @OA\Property(
 *     property="members_count",
 *     type="number",
 *     example=34,
 *   ),
 *   @OA\Property(
 *     property="user_follows_count",
 *     type="number",
 *     example=141,
 *   ),
 *   @OA\Property(
 *     property="posts_count",
 *     type="number",
 *     example=18,
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="ClubInformations",
 *   description="Informations du club pour la scene informations du profil",
 *   allOf={@OA\Schema(ref="#/components/schemas/Club")},
 * )
 * 
 * @OA\Schema(
 *   schema="ClubPosts",
 *   description="Posts du club pour la scene articles du profil",
 *   allOf={@OA\Schema(ref="#/components/schemas/Club")},
 *   @OA\Property(
 *     property="posts",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/ClubPostSimple")
 *   )
 * )
 * 
 * @OA\Schema(
 *   schema="ClubMembers",
 *   description="Membres et demandes adhésion du club pour la scene membres du profil",
 *   @OA\Property(
 *     property="members",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/UserDetails")
 *   ),
 *   @OA\Property(
 *     property="user_join_requests",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/UserDetails")
 *   )
 * )
 */
class Club extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'short_name',
        'address_id',
        'website',
        'organization_id',
        'avatar'
    ];

    protected $with = [
        'address',
        // 'postImages'
        // 'organization',
        // 'members',
        // 'userFollows',
        // 'userJoinRequests',
        // 'posts'
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'address_id',
        'organization_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = [
        'next_hike',
        'user_join_requests_count'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function userFollows()
    {
        return $this->belongsToMany(User::class, 'club_follows');
    }

    public function userJoinRequests()
    {
        return $this->belongsToMany(User::class, 'club_join_requests');
    }

    public function posts()
    {
        return $this->hasMany(ClubPost::class);
    }

    public function postImages()
    {
        return $this->hasMany(ClubPostImage::class);
    }

    public function hikeVttImages()
    {
        return $this->hasMany(HikeVttImage::class);
    }

    public function hikeVtts()
    {
        return $this->hasMany(HikeVtt::class);
    }

    public function getNextHikeAttribute()
    {
        $hike = HikeVtt::where('club_id', $this->id)->whereYear('date', '>=', Carbon::now())->first();
        if ($hike) {
            return $hike;
        }

        return null;
    }

    public function getUserJoinRequestsCountAttribute()
    {
        return $this->userJoinRequests()->count();
    }
}
