<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        "transaction_id",
        "stripe_customer_id",
        "user_id",
        "sponsor_id",
        "user_subscription_id",
        "subscription_price",
        "payment_intent_id",
        "is_payment",
        "payment_response",
        "payment_status"
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
     * userSubscription
     *
     * @return void
     */
    public function userSubscription()
    {
        return $this->belongsTo(\App\Models\UserSubscription::class, 'user_subscription_id');
    }
}
