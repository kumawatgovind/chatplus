<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Category extends Model
{
    use HasFactory, Sortable;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        "parent_id",
        "name",
        "icon",
        "status"
    ];
    
    /**
     * sortable
     *
     * @var array
     */
    public $sortable = ["name", 'status', 'created_at', 'updated_at'];
    
    /**
     * appends
     *
     * @var array
     */
    protected $appends = [
        'icon_url'
    ];
        
    /**
     * Update profile_created Columns.
     *
     * @var string
     */
    public function getIconUrlAttribute()
    {
        $icon = ($this->icon) ? $this->icon : 'no-image-icon.png';
        return asset('storage/category/'.$icon);
        // return !empty($this->profile_image) ? $this->profile_image : "";
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
      * serviceProfile
      *
      * @return void
      */
      public function serviceProfile()
      {
         return $this->hasMany(\App\Models\ServiceProfile::class, 'category_id', 'id');
      }  

     /**
      * serviceProduct
      *
      * @return void
      */
      public function serviceProduct()
      {
         return $this->hasMany(\App\Models\ServiceProduct::class, 'category_id', 'id');
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
            $query->where('name', 'LIKE', '%' . $keyword . '%');
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
