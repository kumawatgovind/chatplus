<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Country extends Model
{
    use HasFactory, Sortable;
    public $sortable = ['title', 'slug', 'created_at', 'updated_at'];
    protected $fillable = ['title', 'slug', 'status'];
    
    /**
     * states
     *
     * @return void
     */
    public function states()
    {
        return $this->hasMany(State::class, 'state_id', 'id');
    }  
}
