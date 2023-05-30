<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Tag extends Model
{
    use HasFactory, Sortable;

    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ["name", "status"];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'pivot'
    ];

    /**
     * mentionedPosts
     *
     * @return void
     */
    public function mentionedPosts()
    {
        return $this->belongsToMany('App\Models\Post')->withTimeStamps();
    }

    /**
     * mentionedComments
     *
     * @return void
     */
    public function mentionedComments()
    {
        return $this->belongsToMany('App\Models\Comment')->withTimeStamps();
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
            $query->where('name', $keyword);
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
        $query->where(function ($query) use ($status) {
            $query->where('status', $status);
        });
        return $query;
    }
}
