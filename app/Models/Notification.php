<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Notification extends Model
{
    use HasFactory, Sortable;
    public $sortable = [];
    protected $fillable = ['type', 'sub_type', 'item_id', 'user_id', 'is_sent'];
   
    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class, 'item_id', 'id');
    }
  
}
