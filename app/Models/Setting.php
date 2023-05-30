<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;
use Kyslik\ColumnSortable\Sortable;

class Setting extends Model
{
    use HasFactory, Sluggable, Sortable;

    protected $fillable = ['title', 'slug', 'config_value', 'manager', 'field_type'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // registering a callback to be executed upon the creation of an activity AR
        // static::creating(function ($model) {
        //     // produce a slug based on the activity title
        //     $slug = \Str::slug($model->title);
        //     // check to see if any other slugs exist that are the same & count them
        //     $count = static::whereRaw("slug LIKE '^{$slug}(-[0-9]+)?$'")->count();
        //     // if other slugs exist that are the same, append the count to the slug
        //     $model->slug = $count ? "{$slug}-{$count}" : $slug;
        // });

        static::updating(function ($model) {
            $model->yamlParse();
        });
        static::saved(function ($model) {
            $model->yamlParse();
        });
        static::updated(function ($model) {
            $model->yamlParse();
        });
        static::deleted(function ($model) {
            $model->yamlParse();
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable():array
    {
        return [
            'slug' => [
                'source' => 'custom_slug',
                'unique' => true,
                'separator' => '_',
                'onUpdate' => true,
            ]
        ];
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtoupper($value);
    }

    /**
     * Scope a query to only include filtered users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', '%' . $keyword . '%');
                $query->where('slug', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('config_value', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function getCustomSlugAttribute()
    {
        if (empty($this->slug)) {
            $slug = trim($this->title);
        } else {
            $slug = trim($this->slug);
        }
        $slug = \Str::slug($slug, '_');
        $slug = strtoupper($slug);
        $query = $this->where("slug", "LIKE", $slug);
        if (!empty($this->id)) {
            $query->where("id", "!=", $this->id);
        }
        $count = $query->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    protected function yamlParse()
    {
        $settings = DB::table('settings')->pluck('config_value', 'slug')->toArray();
        // $query = DB::getQueryLog();
        // $query = end($query);
        // var_dump($query);
        $listYaml = Yaml::dump($settings, 4, 60);
        Storage::disk('configuration')->put('settings.yml', $listYaml);
    }
}
