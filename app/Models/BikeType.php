<?php

namespace App\Models;

use App\Models\Bike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="BikeType",
 *   description="Type de vélo",
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="Vélo Gravel",
 *   )
 * )
 */
class BikeType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function bikes()
    {
        return $this->hasMany(Bike::class);
    }
}
