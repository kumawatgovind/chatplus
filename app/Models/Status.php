<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * table
     *
     * @var string
     */
    protected $table = 'statuses';
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'media_type',
    ];

    /**
     * statusMedias
     *
     * @return void
     */
    public function statusMedias()
    {
        return $this->hasMany(StatusMedia::class, 'status_id', 'id');
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
