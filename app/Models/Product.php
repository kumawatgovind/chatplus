<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    protected $fillable = [
        "user_id",
        "title",
        "locality",
        "city",
        "state",
        "state_id",
        "city_id",
        "locality_id",
        "price",
        "description",
        "latitude",
        "longitude",
        "status",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'added_date',
        'base_path',
    ];

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
     * getBasePathAttribute.
     *
     * @var string
     */
    public function getBasePathAttribute()
    {
        return asset('storage/products/');
    }

    /**
     * productImage
     *
     * @return void
     */
    public function productImage()
    {
        return $this->hasMany(\App\Models\ProductImage::class, 'product_id', 'id');
    }

    /**
     * users
     *
     * @return void
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * state
     *
     * @return void
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
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
     * locality
     *
     * @return void
     */
    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id');
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
            $query->where('title', 'LIKE', '%' . $keyword . '%');
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
