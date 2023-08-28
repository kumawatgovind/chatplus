<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Kyslik\ColumnSortable\Sortable;

class State extends Model
{
    use HasFactory, Sluggable, Sortable;
    public $sortable = ['name', 'slug', 'created_at', 'updated_at'];
    protected $fillable = ['country_id', 'name', 'slug', 'status'];
    
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
        // dd($this->slug);
        if (empty($this->slug)) {
            return strtoupper(trim($this->name));
        } else {
            return strtoupper(trim($this->slug));
        }
    }


    /**
     * country
     *
     * @return void
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * city
     *
     * @return void
     */
    public function city()
    {
        return $this->hasOne(City::class, 'city_id', 'id');
    } 
    
    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class, 'state_id', 'id');
    }
    
    /**
     * serviceProfile
     *
     * @return void
     */
    public function serviceProfile()
    {
        return $this->hasMany(ServiceProfile::class, 'state_id', 'id');
    }
    
    /**
     * address
     *
     * @return void
     */
    public function address()
    {
        return $this->hasMany(Address::class, 'state_id', 'id');
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
            $query->where('states.name', 'LIKE', '%' . $keyword . '%');
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
            $query->where('states.name', 'LIKE', $keyword.'%');
        }
        return $query;
    }
}
