<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Bike;
use App\Models\Club;
use App\Models\Subs;
use App\Models\Address;
use App\Models\PostUser;
use App\Models\ImageUser;
use App\Models\HikeVttHype;
use App\Models\ClubPostLike;
use App\Models\Subscription;
use App\Models\PostUserImage;
use App\Models\ClubPostComment;
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
 *     example="1",
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
 *     @OA\Property(
 *     property="lastname",
 *     type="string",
 *     example="Strueux",
 *     description="Nom du user",
 *     nullable=true
 *   ),
 *   @OA\Property(
 *     property="is_push_notifications",
 *     type="number",
 *     example=1,
 *     description="Est-ce que le user souhaite recevoir des notifications push",
 *   ),
 *   @OA\Property(
 *     property="is_email_notifications",
 *     type="number",
 *     example=1,
 *     description="Est-ce que le user souhaite recevoir des notifications email",
 *   ),
 *   @OA\Property(
 *     property="club_id",
 *     type="number",
 *     example=1,
 *     description="membre du club ayant l'id du club_id",
 *   ),
 *   @OA\Property(
 *     property="is_club_admin",
 *     type="number",
 *     example=1,
 *     description="Est-ce que le user est admin de son club",
 *   ),
 *   @OA\Property(
 *     property="club_name",
 *     type="string",
 *     example="Côte Des Légendes VTT",
 *     description="Nom du club, si le user est dans un club",
 *   ),
 *   @OA\Property(
 *     property="premium_name",
 *     type="string",
 *     example="Premium 1",
 *     description="Nom de l'abonnement du user, s'il est abonné",
 *   ),
 *   @OA\Property(
 *     property="address",
 *     description="address du user",
 *     ref="#/components/schemas/Address"
 *   ),
 * )
 * 
 * @OA\Schema(
 *   schema="UserDetailsCount",
 *   description="Détails du user connecté avec les compteurs de posts - bikes - followers",
 *   allOf={@OA\Schema(ref="#/components/schemas/UserDetails")},
 *   @OA\Property(
 *     property="bikes_count",
 *     type="number",
 *     example=3,
 *   ),
 *   @OA\Property(
 *     property="posts_count",
 *     type="number",
 *     example=12,
 *   ),
 *   @OA\Property(
 *     property="followers_count",
 *     type="number",
 *     example=28,
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
        'email_verified_at',
        'address_id',
        'club'
    ];

    protected $with = [
        'address'
    ];

    protected $appends = [
        'club_name',
        'premium_name',
        'premium',
        'premium_actif'
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
        return $this->belongsToMany(User::class, 'follow_users', 'user_followed_id', 'user_follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follow_users', 'user_follower_id', 'user_followed_id');
    }

    public function subs()
    {
        return $this->hasMany(Subs::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function clubFollows()
    {
        return $this->belongsToMany(Club::class, 'club_follows');
    }

    public function myMembershipRequests()
    {
        return $this->belongsToMany(Club::class, 'club_join_requests');
    }

    public function adminClub()
    {
        return auth()->user()->is_club_admin;
    }

    public function haveClub()
    {
        return auth()->user()->club_id !== NULL;
    }

    public function clubPostComments()
    {
        return $this->hasMany(ClubPostComment::class);
    }

    public function postUserLikes()
    {
        return $this->hasMany(PostUser::class);
    }

    public function postUserImages()
    {
        return $this->hasMany(PostUserImage::class);
    }

    public function clubPostLikes()
    {
        return $this->hasMany(ClubPostLike::class);
    }

    public function hikeVttHypes()
    {
        return $this->hasMany(HikeVttHype::class);
    }

    public function getClubNameAttribute()
    {
        if ($this->club_id) {
            return $this->club->name;
        } else {
            return null;
        }
    }

    public function getPremiumNameAttribute()
    {
        $stripe_customer = $this->stripeCustomer();

        if (!is_null($stripe_customer)) {
            if (!empty($stripe_customer->subscriptions->data)) {
                if (count($stripe_customer->subscriptions->data) > 1) {
                    foreach ($stripe_customer->subscriptions->data as $sub) {
                        if ($sub->cancel_at_period_end && $sub->status == 'active' && $sub->plan->nickname == 'Premium 2') {
                            // On priorise Premium 2 jusqu'a la fin de son abonnement, dans le cas d'un autre abonnement a Premium 1
                            return $sub->plan->nickname;
                        }
                    }

                    return $stripe_customer->subscriptions->data[0]->plan->nickname;
                }

                return $stripe_customer->subscriptions->data[0]->plan->nickname;
            }

            return null;
        }

        return null;
    }

    public function getPremiumAttribute()
    {
        $stripe_customer = $this->stripeCustomer();

        if (is_null($stripe_customer)) {
            return 'inactive';
        }

        if (!empty($stripe_customer->subscriptions->data)) {
            return $stripe_customer->subscriptions->data[0]->status;
        }

        return 'inactive';
    }

    public function getPremiumActifAttribute()
    {
        $stripe_customer = $this->stripeCustomer();

        if (!is_null($stripe_customer)) {
            if (!empty($stripe_customer->subscriptions->data)) {
                if (count($stripe_customer->subscriptions->data) > 1) {
                    foreach ($stripe_customer->subscriptions->data as $sub) {
                        if (!$sub->cancel_at_period_end && $sub->status == 'active') {
                            return $sub->plan->nickname;
                        }
                    }

                    return $stripe_customer->subscriptions->data[0]->plan->nickname;
                }

                return $stripe_customer->subscriptions->data[0]->plan->nickname;
            }

            return null;
        }

        return null;
    }

    // Permet de récupérer le user stripe s'il existe
    public function stripeCustomer()
    {
        if ($this->stripe_customer_id == "0") {
            return null;
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_SK'));
        $stripe_customer = $stripe->customers->retrieve($this->stripe_customer_id, ['expand' => ['subscriptions']]);

        return $stripe_customer;
    }
}
