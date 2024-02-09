<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusMedia extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * table
     *
     * @var string
     */
    protected $table = 'status_medias';

    protected $fillable = [
        "status_id",
        "owner_view",
        "media_type",
        "name",
        'start_status',
        'end_status',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'base_path',
    ];
    /**
     * getBasePathAttribute.
     *
     * @var string
     */
    public function getBasePathAttribute()
    {
        return asset('storage/status/');
    }
    /**
     * status
     *
     * @return void
     */
    public function status()
    {
        return $this->BelongsTo(Status::class, 'status_id', 'id');
    }
        
    /**
     * statusView
     *
     * @return void
     */
    public function statusView()
    {
        return $this->hasMany(StatusView::class, 'status_media_id', 'id');
    }
}
