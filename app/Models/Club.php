<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use App\Models\ClubFollow;
use App\Models\ClubMember;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Club",
 *   description="Club Détails",
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
 *     type="object",
 *     description="Adresse complète du club",
 *     example={
 *       "street_address": "1 Rue Alexandre Baley",
 *       "lat": "48.5740185",
 *       "lng": "-4.3335965",
 *       "region": "Bretagne",
 *       "department": "Finistère",
 *       "department_code": "29",
 *       "city": {
 *         "name": "Lesneven"
 *       },
 *       "zipcode": {
 *         "code": "29260"
 *       },
 *     },
 *     @OA\Property(
 *       property="street_address",
 *       type="string",
 *       description="Adresse du Club",
 *     )
 *   ),
 *   @OA\Property(
 *     property="organization",
 *     type="object",
 *     description="Type d'organisation du club",
 *     example={
 *       "name": "Association"
 *     },
 *   ),
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
        'organization',
        // 'members',
        'userFollows'
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

    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function userFollows()
    {
        return $this->belongsToMany(User::class, 'club_follows');
    }
}
