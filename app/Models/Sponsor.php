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
        return $this->belongsTo(User::class, 'sponsor_user_id');
    }

    /**
     * userSponsored
     *
     * @return void
     */
    public function userSponsored()
    {
        return $this->belongsTo(User::class, 'sponsored_user_id');
    }
    
    /**
     * userEarning
     *
     * @return void
     */
    public function userEarning()
    {
        return $this->hasMany(UserEarning::class, 'sponsor_id');
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
