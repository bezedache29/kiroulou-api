<?php

namespace App\Models;

use App\Models\User;
use App\Models\ClubPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *   schema="ClubPostComment",
 *   description="Commentaire d'un article d'un club par un user",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="user_id",
 *     type="number",
 *     example=10005,
 *   ),
 *   @OA\Property(
 *     property="club_post_id",
 *     type="number",
 *     example=1,
 *   ),
 *   @OA\Property(
 *     property="message",
 *     type="string",
 *     example="Mon super commentaire d'un article du club",
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     example="2022-08-15 14:25:01",
 *   ),
 *   @OA\Property(
 *     property="user_name",
 *     type="string",
 *     example="Simon Strueux",
 *     description="Nom et Prénom du user ou son email si non renseigné"
 *   ),
 *   @OA\Property(
 *     property="user_club_name",
 *     type="string",
 *     example="Côte Des Légendes VTT",
 *     description="Nom du club du user ou la ville du user si non renseigné"
 *   ),
 *   @OA\Property(
 *     property="user_avatar_name",
 *     type="string",
 *     example="mon-avatar.png",
 *     description="Avatar du user ou celui par default si non renseigné"
 *   ),
 * )
 */
class ClubPostComment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'club_post_id',
        'user_id',
        'message'
    ];

    protected $appends = [
        'user_name',
        'user_club_name',
        'user_avatar_name'
    ];

    protected $hidden = [
        'user',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(ClubPost::class);
    }

    // Nom & Prenom du user qui comment ou son Email si non renseigné
    public function getUserNameAttribute()
    {
        if ($this->user->firstname) {
            return $this->user->firstname . ' ' . $this->user->lastname;
        }

        return $this->user->email;
    }

    // Nom du club du user qui comment ou la ville du user si non renseigné
    public function getUserClubNameAttribute()
    {
        if ($this->user->club_id) {
            return $this->user->club_name;
        }

        return $this->user->address->city->name;
    }

    // Avatar du user
    public function getUserAvatarNameAttribute()
    {
        return $this->user->avatar;
    }
}
