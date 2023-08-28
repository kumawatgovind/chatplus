<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Contact extends Model
{
    use HasFactory, Sortable;
    public $sortable = ['name','email','created_at', 'updated_at'];
    protected $fillable = ['user_id','name','email','message'];

    public function listing()
    {
        return $this->belongsTo(\App\Models\Listing::class, 'listing_id');
    }  
}
