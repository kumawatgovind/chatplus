<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ContactSync extends Model
{
    use HasFactory, Sortable;
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
        'sync_by_user_id',
        'if_user_existing_id',
        'code',
        'cid',
        'name',
        'number'
    ];

    public $sortable = ['created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'sync_by_user_id',
        'if_user_existing_id',
        'code',
        'cid',
        'created_at',
        'updated_at',
    ];

    /**
     * syncByUser
     *
     * @return void
     */
    public function syncByUser()
    {
        return $this->belongsTo(User::class, 'sync_by_user_id');
    }

    /**
     * ifUserExisting
     *
     * @return void
     */
    public function ifUserExisting()
    {
        return $this->belongsTo(User::class, 'if_user_existing_id');
    }
}
