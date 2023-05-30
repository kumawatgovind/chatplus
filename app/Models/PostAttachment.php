<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAttachment extends Model
{
    use HasFactory;
    protected $fillable = [
        "post_id",
        "user_id",
        "title",
        "name",
        "url",
        "type",
        "ordering"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'post_id',
        'updated_at',
        'created_at',
    ];
    
    /**
     * post
     *
     * @return void
     */
    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class);
    }
}
