<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatusMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        "status_id",
        "name",
        "ordering"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'status_id',
        'updated_at',
        'created_at',
    ];

    /**
     * userStatus
     *
     * @return void
     */
    public function userStatus()
    {
        return $this->belongsTo(\App\Models\UserStatus::class, 'status_id');
    }
}
