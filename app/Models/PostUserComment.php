<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostUserComment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'message',
        'post_user_id',
        'user_id'
    ];

    protected $with = [
        'user'
    ];

    public function post()
    {
        return $this->belongsTo(PostUser::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
