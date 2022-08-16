<?php

namespace App\Models;

use App\Models\User;
use App\Models\BikeType;
use App\Models\ImageUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="Bike",
 *   description="Vélo du user",
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="Le gravel de folie",
 *     description="Nom que donne le user au vélo"
 *   ),
 *   @OA\Property(
 *     property="brand",
 *     type="string",
 *     example="Specialized",
 *     description="Marque du vélo"
 *   ),
 *   @OA\Property(
 *     property="model",
 *     type="string",
 *     example="S-Works Turbo Creo SL EVO",
 *     description="Modèle de la marque"
 *   ),
 *   @OA\Property(
 *     property="bike_type",
 *     ref="#/components/schemas/BikeType"
 *   ),
 *   @OA\Property(
 *     property="date",
 *     type="string",
 *     example="2022-08-15T21:30:58.000000Z",
 *     description="Date d'achat ou de première mise en circulation"
 *   ),
 *   @OA\Property(
 *     property="weight",
 *     type="string",
 *     example="11.9",
 *     description="Poids en kilogrammes du vélo"
 *   ),
 *   @OA\Property(
 *     property="image",
 *     type="string",
 *     example="spe.png",
 *     description="Image/Photo du vélo"
 *   ),
 * )
 */
class Bike extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'date',
        'weight',
        'image',
        'bike_type_id',
        'user_id'
    ];

    protected $with = [
        'bikeType',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'bike_type_id',
        'user_id'
    ];

    public function bikeType()
    {
        return $this->belongsTo(BikeType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
