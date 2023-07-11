<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    use HasFactory;
    protected $fillable = [
        "service_id",
        "title",
        "name",
        "url",
        "ordering"
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
