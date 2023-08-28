<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Kyslik\ColumnSortable\Sortable;

class Locality extends Model
{
    use HasFactory, Sluggable, Sortable;
    public $sortable = ['name', 'slug', 'created_at', 'updated_at'];
    protected $fillable = ['city_id', 'name', 'slug', 'status'];
     /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'unique' => true,
                'separator' => '-',
                'onUpdate' => true,
            ]
        ];
    }

     /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function getCustomSlugAttribute()
    {
        if (empty($this->slug)) {
            return strtoupper(trim($this->name));
        } else {
            return strtoupper(trim($this->slug));
        }
    }

    /**
     * city
     *
     * @return void
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }  

    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class, 'locality_id', 'id');
    }
    
    /**
     * serviceProfile
     *
     * @return void
     */
    public function serviceProfile()
    {
        return $this->hasMany(ServiceProfile::class, 'locality_id', 'id');
    }
    
    /**
     * address
     *
     * @return void
     */
    public function address()
    {
        return $this->hasMany(Address::class, 'locality_id', 'id');
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
            $query->where('localities.name', 'LIKE', '%' . $keyword . '%');
        }
        return $query;
    }

    /**
     * scopeFilter
     *
     * @param  mixed $query
     * @param  mixed $keyword
     * @return void
     */
    public function scopeMobile($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('localities.name', 'LIKE', $keyword.'%');
        }
        return $query;
    }
}
