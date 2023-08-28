<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProfile extends Model
{
    use HasFactory, Sortable, SoftDeletes;


    protected $fillable = [
        "user_id",
        "category_id",
        "referral_code",
        "service_name",
        "email",
        "contact_person",
        "mobile_number",
        "street_name",
        "building_name",
        'pin_code',
        "locality_id",
        "city_id",
        "state_id",
        "locality",
        "city",
        "state",
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
     * Update base_path Columns.
     *
     * @var string
     */
    public function getBasePathAttribute()
    {
        return asset('storage/services/');
        // return !empty($this->profile_image) ? $this->profile_image : "";
    }


    /**
     * serviceImages
     *
     * @return void
     */
    public function serviceImages()
    {
        return $this->hasMany(\App\Models\ServiceImage::class, 'service_id', 'id');
    }

    /**
     * serviceBusinessHour
     *
     * @return void
     */
    public function serviceBusinessHour()
    {
        return $this->hasMany(\App\Models\ServiceBusinessHour::class, 'service_id', 'id');
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
            $query->where('service_name', 'LIKE', '%' . $keyword . '%');
            $query->orWhere('contact_person', 'LIKE', '%' . $keyword . '%');
            $query->orWhere('mobile_number', 'LIKE', '%' . $keyword . '%');
        }
        return $query;
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
