<?php

namespace App\Models;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClubMember extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'club_id',
        'is_user_admin',
    ];

    protected $with = [
        'club'
    ];

    // Permet de cacher ces valeurs
    protected $hidden = [
        'id',
        'user_id',
        'club_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
