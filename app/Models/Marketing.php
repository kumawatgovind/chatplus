<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Marketing extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    protected $fillable = [
        "type",
        "name",
        "media_name",
        "status"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'media_name'
    ];
    
    /**
     * appends
     *
     * @var array
     */
    protected $appends = [
        'media_url',
        'added_date'
    ];

    /**
     * Update profile_created Columns.
     *
     * @var string
     */
    public function getMediaUrlAttribute()
    {
        $outPut = '';
        if ($this->type == 'image') {
            $mediaName = ($this->media_name) ? $this->media_name : 'no-image-icon.png';
            $outPut = asset('storage/banner/'.$mediaName);
        } else {
            $outPut = $this->media_name;
        }
        return $outPut;
    } 

    /**
     * getAddedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getAddedDateAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }
    /**
     * scopeFilter
     *
     * @param  mixed $query
     * @param  mixed $keyword
     * @return void
     */
    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }
        return $query;
    }
    
    /**
     * scopeStatus
     *
     * @param  mixed $query
     * @param  mixed $status
     * @return void
     */
    public function scopeStatus($query, $status = 1)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($status) {
                $query->where('status', $status);
            });
        }
        return $query;
    }
}
