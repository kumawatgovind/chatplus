<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        "service_product_id",
        "name",
        "ordering"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'service_product_id',
        'updated_at',
        'created_at',
    ];
    
    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->belongsTo(\App\Models\ServiceProduct::class);
    }
}
