<?php

namespace App\Models;

use App\Models\User;
use App\Models\PostUserLike;
use App\Models\PostUserImage;
use App\Models\PostUserComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostUser extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'user_id'
    ];

    protected $with = [
        'postUserImages',
        'postUserComments',
        'postUserLikes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postUserImages()
    {
        return $this->hasMany(PostUserImage::class);
    }

    public function postUserComments()
    {
        return $this->hasMany(PostUserComment::class);
    }

    public function postUserLikes()
    {
        return $this->hasMany(PostUserLike::class);
    }
}
