<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = [
        "product_id",
        "name",
        "ordering"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'product_id',
        'updated_at',
        'created_at',
    ];

    /**
     * product
     *
     * @return void
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
