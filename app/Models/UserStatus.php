<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;
    /**
     * table
     *
     * @var string
     */
    protected $table = 'user_status';
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status_type',
        'status_text',
        'status'
    ];

    /**
     * userStatusMedia
     *
     * @return void
     */
    public function userStatusMedia()
    {
        return $this->hasMany(\App\Models\UserStatusMedia::class, 'status_id', 'id');
    }

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
