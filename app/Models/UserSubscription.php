<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "subscription_id",
        "start_date",
        "end_date",
        "subscription_price",
        "stripe_token",
        "is_active"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * subscription
     *
     * @return void
     */
    public function subscription()
    {
        return $this->belongsTo(\App\Models\Subscription::class, 'subscription_id');
    }


    /**
     * subscriptionPayment
     *
     * @return void
     */
    public function subscriptionPayment()
    {
        return $this->hasOne(\App\Models\SubscriptionPayment::class, 'user_subscription_id', 'id');
    }
}
