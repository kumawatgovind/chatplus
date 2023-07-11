<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBusinessHour extends Model
{
    use HasFactory;
    protected $fillable = [
        "service_id",
        "day_name",
        "is_open",
        "time",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'service_id',
        'updated_at',
        'created_at',
    ];
    
    /**
     * serviceProfile
     *
     * @return void
     */
    public function serviceProfile()
    {
        return $this->belongsTo(\App\Models\ServiceProfile::class);
    }
}
