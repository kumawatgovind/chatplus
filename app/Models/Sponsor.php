<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Sponsor extends Model
{
    use HasFactory, Sortable;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "sponsor_user_id",
        "sponsored_user_id",
    ];

    /**
     * sortable
     *
     * @var array
     */
    public $sortable = ['created_at', 'updated_at'];

    /**
     * appends
     *
     * @var array
     */
    protected $appends = [];

    /**
     * userSponsor
     *
     * @return void
     */
    public function userSponsor()
    {
        return $this->belongsTo(\App\Models\User::class, 'sponsor_user_id');
    }

    /**
     * userSponsored
     *
     * @return void
     */
    public function userSponsored()
    {
        return $this->belongsTo(\App\Models\User::class, 'sponsored_user_id');
    }

    /**
     * parent
     *
     * @return void
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'referring_user_id');
    }

    /**
     * children
     *
     * @return void
     */
    public function children()
    {
        return $this->hasMany(self::class, 'referring_user_id', 'id');
    }


    /**
     * childrenSponsor
     *
     * @return void
     */
    public function childrenSponsor()
    {
        return $this->children()->with('childrenSponsor');
    }
}
