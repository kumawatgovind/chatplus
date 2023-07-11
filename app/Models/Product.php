<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "user_id",
        "title",
        "locality",
        "city",
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
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
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
            $query->where('title', $keyword);
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
