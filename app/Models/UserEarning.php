<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class UserEarning extends Model
{
    use HasFactory, Sortable;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "earning",
        "subscription_price",
        "admin_earning",
    ];

    /**
     * sortable
     *
     * @var array
     */
    public $sortable = ['created_at', 'updated_at'];

    /**
     * appends
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
}
