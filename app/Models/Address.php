<?php

namespace App\Models;

use App\Models\City;
use App\Models\Zipcode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'city_id',
        'zipcode_id',
        'user_id',
    ];

    protected $with = [
        'city',
        'zipcode',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zipcode()
    {
        return $this->belongsTo(Zipcode::class);
    }
}
