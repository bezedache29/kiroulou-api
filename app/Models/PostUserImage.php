<?php

namespace App\Models;

use App\Models\PostUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostUserImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'post_user_id'
    ];

    public function post()
    {
        return $this->belongsTo(PostUser::class);
    }
}
