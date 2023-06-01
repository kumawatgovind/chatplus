<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ServiceProfile extends Model
{
    use HasFactory, Sortable;

    
    protected $fillable = [
        "user_id",
        "category_id",
        "service_name",
        "email",
        "contact_person",
        "mobile_number",
        "street_name",
        "building_name",
        'pin_code',
        'city',
        'state',
        'country',
        'website',
        'description',
        'latitude',
        'longitude',
    ];
    
    public $sortable = ['status', 'created_at', 'updated_at'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'added_date',
        'human_readable',
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
     * getHumanReadableAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getHumanReadableAttribute($value)
    {
        return !empty($this->updated_at) ? $this->updated_at->diffForHumans() : "";
    }

    
    /**
     * serviceImages
     *
     * @return void
     */
    public function serviceImages()
    {
        return $this->hasMany(\App\Models\ServiceImages::class, 'service_id', 'id');
    }

    
    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
    
    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', 0);;
    }
    /**
     * scopeUserFilter
     *
     * @param  mixed $query
     * @param  mixed $user_id
     * @return void
     */
    public function scopeUserFilter($query, $user_id)
    {
        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }
        return $query;
    }
}
