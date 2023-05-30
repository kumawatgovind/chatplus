<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSync extends Model
{
    use HasFactory;    
    /**
     * table
     *
     * @var string
     */
    protected $table = 'contact_sync';    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'cid',
        'name',
        'number'
    ];
    
    /**
     * users
     *
     * @return void
     */
    public function users()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    
}
