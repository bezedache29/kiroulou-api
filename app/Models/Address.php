<?php

namespace App\Models;

use App\Models\City;
use App\Models\HikeVtt;
use App\Models\Zipcode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Address",
 *   description="Adresse simplifié",
 *   @OA\Property(
 *     property="street_address",
 *     type="string",
 *     example="Ma super adresse",
 *     description="Rue / lieu-dit"
 *   ),
 *   @OA\Property(
 *     property="lat",
 *     type="string",
 *     example="48.5740185",
 *     description="Lattitude"
 *   ),
 *   @OA\Property(
 *     property="lng",
 *     type="string",
 *     example="-4.3335965",
 *     description="Longitude"
 *   ),
 *   @OA\Property(
 *     property="region",
 *     type="string",
 *     example="Ma super région",
 *     description="Region"
 *   ),
 *   @OA\Property(
 *     property="department",
 *     type="string",
 *     example="Mon super département",
 *     description="Département"
 *   ),
 *   @OA\Property(
 *     property="department_code",
 *     type="string",
 *     example="29",
 *     description="Code du département"
 *   ),
 *   @OA\Property(
 *     property="city",
 *     ref="#/components/schemas/City"
 *   ),
 *   @OA\Property(
 *     property="zipcode",
 *     ref="#/components/schemas/Zipcode"
 *   ),
 * )
 */
class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'street_address',
        'lat',
        'lng',
        'region',
        'department',
        'department_code',
        'city_id',
        'zipcode_id',
    ];

    protected $with = [
        'city',
        'zipcode',
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'id',
        'city_id',
        'zipcode_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zipcode()
    {
        return $this->belongsTo(Zipcode::class);
    }

    public function hikeVtts()
    {
        return $this->hasMany(HikeVtt::class);
    }
}
