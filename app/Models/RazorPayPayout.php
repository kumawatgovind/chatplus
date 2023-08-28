<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class RazorPayPayout extends Model
{
    use HasFactory, Sortable;
    
    protected $fillable = [
        "user_id",
        "payout_id",
        "amount",
        "fees",
        "tax",
        "status",
        "payouts",
    ];

    public $sortable = ['created_at', 'updated_at'];
}
