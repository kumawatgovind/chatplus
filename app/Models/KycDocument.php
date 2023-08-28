<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KycDocument extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * table
     *
     * @var string
     */
    protected $table = 'kyc_document';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'aadhar_number',
        'aadhar_front',
        'aadhar_back',
        'pan_number',
        'pan_front',
        'bank_account_number',
        'account_holder_name',
        'bank_ifsc_code',
        'bank_name',
        'passbook_image',
        'is_default',
        'is_kyc',
        'reason',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'document_base_url',
        'document_added_date',
        'document_updated_date',
    ];

    /**
     * Update profile_created Columns.
     *
     * @var string
     */
    public function getDocumentBaseUrlAttribute()
    {
        return asset('storage/document/');
    }

    /**
     * getDocumentDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getDocumentAddedDateAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }

    /**
     * getDocumentUpdatedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getDocumentUpdatedDateAttribute($value)
    {
        return !empty($this->updated_at) ? strtotime($this->updated_at) : "";
    }

    /**
     * The belongs to Relationship
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
