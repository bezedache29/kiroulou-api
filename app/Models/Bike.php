<?php

namespace App\Models;

use App\Models\User;
use App\Models\BikeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'bikeType'
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
