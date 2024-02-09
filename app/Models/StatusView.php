<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusView extends Model
{
    use HasFactory;
    protected $fillable = [
        "status_id",
        "status_media_id",
        "view_user_id",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'status_id',
        'status_media_id',
        'updated_at',
        'created_at',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_seen',
    ];
    
     /**
     * getStatusSeenAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getStatusSeenAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }

    /**
     * Status
     *
     * @return void
     */
    public function Status()
    {
        return $this->belongsTo(StatusMedia::class, 'status_media_id');
    }

    /**
     * viewer
     *
     * @return void
     */
    public function viewer()
    {
        return $this->belongsTo(User::class, 'view_user_id');
    }
}
