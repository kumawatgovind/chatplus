<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class ServiceProduct extends Model
{
    use HasFactory, SoftDeletes, Sortable;

    protected $fillable = [
        "product_type",
        "product_for",
        "user_id",
        "category_id",
        "sub_category_id",
        "title",
        "locality_id",
        "city_id",
        "state_id",
        "locality",
        "city",
        "state",
        "address",
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
        return asset('storage/services/products/');
    }




    /**
     * serviceProductImage
     *
     * @return void
     */
    public function serviceProductImage()
    {
        return $this->hasMany(\App\Models\ServiceProductImage::class, 'service_product_id', 'id');
    }

    /**
     * propertyAttribute
     *
     * @return void
     */
    public function propertyAttribute()
    {
        return $this->hasOne(\App\Models\PropertyAttribute::class, 'service_product_id', 'id');
    }
    /**
     * serviceUser
     *
     * @return void
     */
    public function serviceUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * category
     *
     * @return void
     */
    public function subCategory()
    {
        return $this->belongsTo(\App\Models\Category::class, 'sub_category_id');
    }

    /**
     * userServiceProductBookmark
     *
     * @return obj
     */
    public function userServiceProductBookmark()
    {
        return $this->belongsToMany('App\Models\User', 'service_product_bookmark', 'service_product_id', 'user_id')->withTimeStamps();
    }

    /**
     * userServiceProductBookmarkUpdate
     *
     * @param  mixed $user
     * @return void
     */
    public function userServiceProductBookmarkUpdate($user)
    {
        return $this->userServiceProductBookmark()->toggle($user);
    }

    public function is_bookmarked(User $user)
    {
        return $this->userServiceProductBookmark->contains($user);
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
            $query->where(function ($query) use ($keyword) {
                $query->orWhere('service_products.title', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }
    /* scopeStatus
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
