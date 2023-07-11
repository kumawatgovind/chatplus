<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyAttribute extends Model
{
    use HasFactory;
    protected $fillable = [
        "service_product_id",
        "property_requirement",
        "property_category",
        "property_category_type",
        "project_authority",
        "property_bedroom",
        "property_bathroom",
        "property_floor",
        "property_furnishing",
        "property_facing",
        "property_status",
        "property_condition",
        "property_carpet_area",
        "carpet_area_unit",
        "property_super_area",
        "super_area_unit",
        "property_length",
        "length_unit",
        "property_breadth",
        "breadth_unit",
        "property_road_width",
        "road_width_unit"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        "service_product_id",
        "property_category_type",
        "property_bedroom",
        "property_bathroom",
        "property_floor",
        "property_furnishing",
        "property_facing",
        "property_status",
        "property_condition",
        "property_carpet_area",
        "carpet_area_unit",
        "property_super_area",
        "super_area_unit",
        "property_length",
        "length_unit",
        "property_breadth",
        "breadth_unit",
        "property_road_width",
        "road_width_unit",
        'updated_at',
        'created_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        "category_type",
        "bedroom",
        "bathroom",
        "floor",
        "furnishing",
        "facing",
        "status",
        "condition",
        "carpet_area",
        "carpet_unit",
        "super_area",
        "super_unit",
        "length",
        "lengths_unit",
        "breadth",
        "breadths_unit",
        "road_width",
        "road_side_unit"
    ];

    /**
     * getCategoryTypeAttribute
     *
     * @param  
     * @return string
     */
    public function getCategoryTypeAttribute()
    {
        $response = '';
        if ($this->serviceProduct) {
            if ($this->serviceProduct->product_type == 'Property') {
                $productType = $this->serviceProduct->product_type;
                $productFor = $this->serviceProduct->product_for;
                if (!empty($productType) && !empty($productFor)) {
                    // $productTypeProductFor = config('constants.MASTER_DATA')['type'][$productType][$productFor];
                    // $response = $productTypeProductFor[$this->property_category][$this->property_category_type];
                    $productTypeProductFor = config('constants.MASTER_DATA')['type'][$productType][$productFor];
                    if ($productFor == 'Requirement') {
                        $response = $productTypeProductFor[$this->property_requirement][$this->property_category][$this->property_category_type];
                    } else {
                        $response = $productTypeProductFor[$this->property_category][$this->property_category_type];
                    }
                    // $response = $productTypeProductFor;
                }
            }
        }
        return $response;
    }
    /**
     * getBedroomAttribute
     *
     * @param  
     * @return string
     */
    public function getBedroomAttribute()
    {
        return !empty($this->property_bedroom) ? config('constants.MASTER_DATA')['bedroom'][$this->property_bedroom] : "";
    }
    /**
     * getBathroomAttribute
     *
     * @param  
     * @return string
     */
    public function getBathroomAttribute()
    {
        return !empty($this->property_bathroom) ? config('constants.MASTER_DATA')['bathroom'][$this->property_bathroom] : "";
    }
    /**
     * getFloorAttribute
     *
     * @param  
     * @return string
     */
    public function getFloorAttribute()
    {
        return !empty($this->property_floor) ? config('constants.MASTER_DATA')['floor'][$this->property_floor] : "";
    }
    /**
     * getFurnishingAttribute
     *
     * @param  
     * @return string
     */
    public function getFurnishingAttribute()
    {
        return !empty($this->property_furnishing) ? config('constants.MASTER_DATA')['furnishing'][$this->property_furnishing] : "";
    }
    /**
     * getFacingAttribute
     *
     * @param  
     * @return string
     */
    public function getFacingAttribute()
    {
        return !empty($this->property_facing) ? config('constants.MASTER_DATA')['facing'][$this->property_facing] : "";
    }
    /**
     * getStatusAttribute
     *
     * @param  
     * @return string
     */
    public function getStatusAttribute()
    {
        return !empty($this->property_status) ? config('constants.MASTER_DATA')['property_status'][$this->property_status] : "";
    }
    /**
     * getConditionAttribute
     *
     * @param  
     * @return string
     */
    public function getConditionAttribute()
    {
        return !empty($this->property_condition) ? config('constants.MASTER_DATA')['condition'][$this->property_condition] : "";
    }
    /**
     * getCarpetAreaAttribute
     *
     * @param  
     * @return string
     */
    public function getCarpetAreaAttribute()
    {
        return !empty($this->property_carpet_area) ? $this->property_carpet_area : "";
    }
    /**
     * getCarpetUnitAttribute
     *
     * @param  
     * @return string
     */
    public function getCarpetUnitAttribute()
    {
        return !empty($this->carpet_area_unit) ? config('constants.MASTER_DATA')['area'][$this->carpet_area_unit] : "";
    }
    /**
     * getSuperAreaAttribute
     *
     * @param  
     * @return string
     */
    public function getSuperAreaAttribute()
    {
        return !empty($this->property_super_area) ? $this->property_super_area : "";
    }
    /**
     * getSuperUnitAttribute
     *
     * @param  
     * @return string
     */
    public function getSuperUnitAttribute()
    {
        return !empty($this->super_area_unit) ? config('constants.MASTER_DATA')['area'][$this->super_area_unit] : "";
    }
    /**
     * getLengthAttribute
     *
     * @param  
     * @return string
     */
    public function getLengthAttribute()
    {
        return !empty($this->property_length) ? $this->property_length : "";
    }
    /**
     * getLengthUnitAttribute
     *
     * @param  
     * @return string
     */
    public function getLengthsUnitAttribute()
    {
        return !empty($this->length_unit) ? config('constants.MASTER_DATA')['area'][$this->length_unit] : "";
    }
    /**
     * getBreadthAttribute
     *
     * @param  
     * @return string
     */
    public function getBreadthAttribute()
    {
        return !empty($this->property_breadth) ? $this->property_breadth : "";
    }
    /**
     * getBreadthsUnitAttribute
     *breadths_unit
     * @param  
     * @return string
     */
    public function getBreadthsUnitAttribute()
    {
        return !empty($this->breadth_unit) ? config('constants.MASTER_DATA')['area'][$this->breadth_unit] : "";
    }
    /**
     * getRoadWidthAttribute
     *
     * @param  
     * @return string
     */
    public function getRoadWidthAttribute()
    {
        return !empty($this->property_road_width) ? $this->property_road_width : "";
    }
    /**
     * getRoadSideUnitAttribute
     *
     * @param  
     * @return string
     */
    public function getRoadSideUnitAttribute()
    {
        return !empty($this->road_width_unit) ? config('constants.MASTER_DATA')['area'][$this->road_width_unit] : "";
    }

    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->belongsTo(\App\Models\ServiceProduct::class);
    }
}
