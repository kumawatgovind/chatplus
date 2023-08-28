<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, Sortable, SoftDeletes;


    protected $fillable = [
        "user_id",
        "street_name",
        "building_name",
        "pin_code",
        "locality_id",
        "city_id",
        "state_id",
        "locality",
        "city",
        "state",
        "latitude",
        "longitude",
        "is_recent",
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
     * users
     *
     * @return void
     */
    public function user()
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
        $query->where(function ($query) use ($status) {
            $query->where('status', $status);
        });
        return $query;
    }
}
