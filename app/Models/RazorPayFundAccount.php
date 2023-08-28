<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class RazorPayFundAccount extends Model
{
    use HasFactory, Sortable;
    
    protected $fillable = [
        "user_id",
        "payout_account_id",
        "fund_accounts",
    ];

    public $sortable = ['created_at', 'updated_at'];
}
