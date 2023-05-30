<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailHook extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'status'];

    /**
     * Get the comments for the blog post.
     */
    public function templates()
    {
        return $this->hasMany(EmailPreference::class);
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
