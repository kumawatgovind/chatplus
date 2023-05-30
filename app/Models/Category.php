<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use HasFactory, Sluggable, Sortable;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "title",
        "slug",
        "parent_id",
        "status"
    ];
    
    /**
     * sortable
     *
     * @var array
     */
    public $sortable = ["title",  "slug", 'status', 'created_at', 'updated_at'];
    
    /**
     * appends
     *
     * @var array
     */
    // protected $appends = [
    //     'parent'
    // ];
        
     /**
      * sluggable
      *
      * @return array
      */
     public function sluggable():array
     {
         return [
             'slug' => [
                 'source' => 'custom_slug',
                 'unique' => true,
                 'separator' => '-',
                 'onUpdate' => true,
             ]
         ];
     }
      
     /**
      * parent
      *
      * @return void
      */
     public function parent()
     {
         return $this->belongsTo(self::class, 'parent_id');
     }
      
     /**
      * children
      *
      * @return void
      */
     public function children()
     {
        return $this->hasMany(self::class, 'parent_id', 'id');
     }     
     /**
      * users
      *
      * @return void
      */
     public function users()
    {
        return $this->belongsToMany(\App\Models\User::class,'category_users','category_id','user_id');
    }
 
    /**
     * childrenCategory
     *
     * @return void
     */
    public function childrenCategory()
    {
        return $this->children()->with('childrenCategory');
    }
    
    
    /**
     * scopeFilter
     *
     * @param  mixed $query
     * @param  mixed $keyword
     * @return void
     */
    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }
    
    /**
     * scopeStatus
     *
     * @param  mixed $query
     * @param  mixed $status
     * @return void
     */
    public function scopeStatus($query, $status = 1)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($status) {
                $query->where('status', $status);
            });
        }
        return $query;
    }
}
