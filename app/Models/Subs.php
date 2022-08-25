<?php

namespace App\Models;

use App\Models\User;
use App\Models\SubscriptionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subs extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'subscription_type_id',
        'start_at',
        'end_at',
        'cancel_at_perdiod_end',
        'status',
        'latest_invoice_id',
        'default_payment_method_id',
    ];

    protected $with = [
        'subscriptionType'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionType::class);
    }

    public function getSubscriptionTypeName()
    {
        return $this->subscriptionType->name;
    }
}
