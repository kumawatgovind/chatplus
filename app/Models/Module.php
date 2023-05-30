<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Module extends Model
{
    use HasFactory,  Sortable;
    protected $fillable = ["title", "slug", "ordering", "status"];

    public $sortable = ["title", 'status', 'created_at', 'updated_at'];


    /* SLUG Works come here */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'custom_slug',
                'unique' => true,
                'separator' => '-',
                'onUpdate' => true,
            ]
        ];
    }


    public function roles()
    {
        return $this->belongsToMany(\App\Models\AdminRole::class);
    }



    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function getCustomSlugAttribute()
    {
        if (empty($this->slug)) {
            return strtoupper(trim($this->title));
        } else {
            return strtoupper(trim($this->slug));
        }
    }

    /* End of slug */
    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('question', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }

    public function scopeStatus($query, $status = 1)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($status) {
                $query->where('status', $status);
            });
        }
        return $query;
    }

    public function ScopeModules($query)
    {

        return $query->where('status', 1)->orderBy('ordering', 'Asc');
    }
}
