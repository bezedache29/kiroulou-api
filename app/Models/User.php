<?php

namespace App\Models;

use App\Models\Bike;
use App\Models\Club;
use App\Models\Address;
use App\Models\PostUser;
use App\Models\ClubFollow;
use App\Models\ClubMember;
use App\Models\Subscription;
use App\Models\PostUserComment;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @OA\Schema(
 *   schema="User",
 *   description="User Simple",
 *   @OA\Property(
 *     property="id",
 *     type="number",
 *     example="0",
 *     description="Id du user",
 *   ),
 *   @OA\Property(
 *     property="email",
 *     type="string",
 *     example="simon-strueux@gmail.com",
 *     description="Email du user",
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     description="Date de création du user",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="updated_at",
 *     type="string",
 *     format="date-time",
 *     description="Date d'édition du user",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="deleted_at",
 *     type="string",
 *     format="date-time",
 *     description="Date d'e suppression du user",
 *     nullable=true
 *   ),
 * )
 * 
 * 
 * @OA\Schema(
 *   schema="UserLoggedIn",
 *   description="Token du user une fois connecté",
 *   @OA\Property(
 *     property="token",
 *     type="string",
 *     example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
 *     description="Token généré par sanctum, après connexion du user",
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="UserDetails",
 *   description="Détails du user connecté",
 *   allOf={@OA\Schema(ref="#/components/schemas/User")},
 *   @OA\Property(
 *     property="firstname",
 *     type="string",
 *     example="Simon",
 *     description="Prénom du user",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="lastname",
 *     type="string",
 *     example="Strueux",
 *     description="Nom du user",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="address_id",
 *     type="number",
 *     example="1",
 *     description="Adresse du user (relation avec la table Address)",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="is_push_notifications",
 *     type="number",
 *     example="1",
 *     description="Est-ce que le user souhaite recevoir des notifications push",
 *   ),
 *   @OA\Property(
 *     property="is_email_notifications",
 *     type="number",
 *     example="1",
 *     description="Est-ce que le user souhaite recevoir des notifications email",
 *   ),
 * )
 */
class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'firstname',
    'lastname',
    'email',
    'password',
    'adresse_id',
    'avatar',
    'is_push_notifications',
    'is_email_notifications',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function address()
  {
    return $this->belongsTo(Address::class);
  }

  public function posts()
  {
    return $this->hasMany(PostUser::class);
  }

  public function comments()
  {
    return $this->hasMany(PostUserComment::class);
  }

  public function bikes()
  {
    return $this->hasMany(Bike::class);
  }

  public function followers()
  {
    return $this->belongsToMany(User::class, 'follow_users', 'user_followed_id', 'user_follower_id')->withTimestamps();
  }

  public function followings()
  {
    return $this->belongsToMany(User::class, 'follow_users', 'user_follower_id', 'user_followed_id')->withTimestamps();
  }

  public function subscriptions()
  {
    return $this->hasMany(Subscription::class);
  }

  public function clubMember()
  {
    return $this->hasOne(ClubMember::class);
  }

  public function clubFollows()
  {
    return $this->belongsToMany(Club::class, 'club_follows');
  }
}
