<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
   
    protected $dates = ['deleted_at'];
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'post_id', 'parent_id', 'comment', 'media_url', 'post_type'];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'commented_date',
        'comment_added'
    ];

    /**
     * getPostedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getCommentedDateAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }

    /**
     * getPostedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getCommentAddedAttribute($value)
    {
        return !empty($this->created_at) ? $this->created_at->diffForHumans() : "";
    }

    /**
     * The belongs to Relationship
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The belongs to Relationship
     *
     * @var array
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    
    /**
     * tags
     *
     * @return void
     */
    public function mentionedTags() : object
    {
        return $this->belongsToMany('App\Models\Tag')->withTimeStamps();
    }

    /**
     * mentionedUsers
     *
     * @return void
     */
    public function mentionedUsers()
    {
        return $this->belongsToMany('App\Models\User')->withTimeStamps();
    }

    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
