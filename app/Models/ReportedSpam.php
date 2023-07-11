<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ReportedSpam extends Model
{
    use HasFactory,  Sortable;

    protected $table = 'reported_spams';

    protected $fillable = ["item_id", "type", "description", "reported_by"];

    public $sortable = ["type", 'reported_by', 'created_at', 'updated_at'];

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'item_id');
    }

    /**
     * userByReport
     *
     * @return void
     */
    public function userByReport()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'reported_by');
    }

    /* End of slug */
    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('description', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }
}
