<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use App\Models\ClubPost;
use App\Models\ClubFollow;
use App\Models\ClubMember;
use App\Models\Organization;
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
 *     property="shortName",
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
 *     property="address",
 *     description="Adresse du club",
 *     ref="#/components/schemas/Address"
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
 *     @OA\Items(ref="#/components/schemas/ClubPost")
 *   )
 * )
 * 
 * @OA\Schema(
 *   schema="ClubMembers",
 *   description="Membres et demandes adhésion du club pour la scene membres du profil",
 *   allOf={@OA\Schema(ref="#/components/schemas/Club")},
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
        'shortName',
        'address_id',
        'website',
        'organization_id',
        'avatar'
    ];

    protected $with = [
        'address',
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

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // public function members()
    // {
    //     return User::where('club_id', $this->id)->get();
    // }

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
}
