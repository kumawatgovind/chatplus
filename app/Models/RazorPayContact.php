<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class RazorPayContact extends Model
{
    use HasFactory, Sortable;
    
    protected $fillable = [
        "user_id",
        "payout_contact_id",
        "contacts",
    ];

    public $sortable = ['created_at', 'updated_at'];
}
