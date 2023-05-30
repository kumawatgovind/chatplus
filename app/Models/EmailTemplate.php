<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['email_hook_id', 'subject', 'description', 'footer_text', 'email_preference_id', 'status'];


    /**
     * Get the email that owns the hook.
     */
    public function email_hook()
    {
        return $this->belongsTo(EmailHook::class);
    }

    /**
     * Get the email that owns the hook.
     */
    public function email_preference()
    {
        return $this->belongsTo(EmailPreference::class);
    }
}
