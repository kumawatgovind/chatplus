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
        'user_id',
        'code',
        'cid',
        'name',
        'number'
    ];

    public $sortable = ['created_at', 'updated_at'];

    /**
     * users
     *
     * @return void
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
