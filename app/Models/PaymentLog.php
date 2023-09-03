<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class PaymentLog extends Model
{
    use HasFactory, Sortable;
    protected $fillable = [
        "response", "type"
    ];

    public $sortable = ['created_at', 'updated_at'];
}
