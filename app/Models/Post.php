<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Post extends Model
{
    use HasFactory, Sortable;

    
    protected $fillable = [
        "user_id",
        "category_id",
        "original_post_user_id",
        "post_id",
        "post_type",
        "post_visibility",
        "description",
        "status",
        'position'
    ];
    
    public $sortable = ['status', 'created_at', 'updated_at'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
        'repost_at',
        'repost_count',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'posted_date',
        'human_readable',
        'is_post_like',
        'is_repost',
        'is_post_view'
    ];

    /**
     * getPostedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getPostedDateAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }

    /**
     * getPostedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getHumanReadableAttribute($value)
    {
        return !empty($this->updated_at) ? $this->updated_at->diffForHumans() : "";
    }

    /**
     * getIsPostLikeAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getIsPostLikeAttribute($value)
    {
        if (!empty(\Request::get('Auth'))) {
            return $this->postLike()
            ->where('post_like.user_id', \Request::get('Auth')->id)
            ->exists();
        }else {
            return false;
        }
    }
    

    /**
     * getIsPostLikeAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getIsRepostAttribute($value)
    {
        if (!empty(\Request::get('Auth'))) {
            return $this->rePost()
            ->where('post_repost.user_id', \Request::get('Auth')->id)
            ->exists();
        }else {
            return false;
        }
    }

    /**
     * getIsPostViewAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getIsPostViewAttribute($value)
    {
        if (!empty(\Request::get('Auth'))) {
            return $this->postView()
            ->where('post_view.user_id', \Request::get('Auth')->id)
            ->exists();
        }else {
            return false;
        }
    }

    /**
     * originalPost
     *
     * @return void
     */
    public function originalPost()
    {
        return $this->belongsTo(\App\Models\Post::class, 'post_id');
    }
    /**
     * attachments
     *
     * @return void
     */
    public function attachments()
    {
        return $this->hasMany(\App\Models\PostAttachment::class, 'post_id', 'id');
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
     * postView
     *
     * @return void
     */
    public function postView()
    {
        return $this->belongsToMany('App\Models\User', 'post_view', 'post_id', 'user_id')->withTimeStamps();
    }
    
    /**
     * postViewUpdate
     *
     * @param  mixed $user
     * @return void
     */
    public function postViewUpdate($user)
    {
        return $this->postView()->toggle($user);
    }

    /**
     * rePost
     *
     * @return void
     */
    public function rePost()
    {
        return $this->belongsToMany('App\Models\User', 'post_repost', 'post_id', 'user_id')->withTimeStamps();
    }
    
    /**
     * rePostUpdate
     *
     * @param  mixed $user
     * @return void
     */
    public function rePostUpdate($user)
    {
        return $this->rePost()->attach($user);
    }

    /**
     * postLike
     *
     * @return void
     */
    public function postLike()
    {
        return $this->belongsToMany('App\Models\User', 'post_like', 'post_id', 'user_id')->withTimeStamps();
    }

    /**
     * postLikeUpdate
     *
     * @param  mixed $user
     * @return void
     */
    public function postLikeUpdate($user)
    {
        return $this->postLike()->toggle($user);
    }

    /**
     * tags
     *
     * @return void
     */
    public function mentionedTags()
    {
        return $this->belongsToMany('App\Models\Tag')->withTimeStamps();
    }
       
    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }
    
    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->where('parent_id', 0);;
    }
    /**
     * scopeUserFilter
     *
     * @param  mixed $query
     * @param  mixed $user_id
     * @return void
     */
    public function scopeUserFilter($query, $user_id)
    {
        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
        }
        return $query;
    }
}
