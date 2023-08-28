<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Password;
use Mail;

class AdminUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $guard = 'admin';

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'email',
        'dob',
        'email_verified_at',
        'password',
        'role_id',
        'status',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date:Y-m-d',
    ];

    /**
     * Bootstrap eloquent.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            $model->checkEmailVerified();
        });
        static::updated(function ($model) {
            $model->sendVerificationEmail();
        });
        // static::creating(function ($model) {
        //     $model->api_token = \Str::random(120);
        // });
    }

    /**
     * Get the all roles those associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(\App\Models\AdminRole::class)->orderBy('id', 'ASC');
    }

    /**
     * Set the user's name with ucfirst.
     *
     * @param  string  $value
     * @return string
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    /**
     * Get the user's name with ucfirst.
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * When eloquent update event are trigger then we need to check email is dirty or not.
     *
     * @return void
     */
    protected function checkEmailVerified()
    {
        if ($this->isDirty('email')) {
            $this->attributes['email_verified_at'] = NULL;
        }
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // $url = 'https://example.com/reset-password?token='.$token;
        // $this->notify(new ResetPasswordNotification($url));

        $verificationUrl = $this->verificationUrl($this);

        if (\Request::route()->getName() == "admin.password.email") {
            $hook = "forgot-password-email";
            //$replacement['RESET_PASSWORD_URL'] = $token;
            $replacement['RESET_PASSWORD_URL'] = url("/admin/reset-password/{$token}/?email=" . (base64_encode(request('email'))));
        } else if (\Request::route()->getName() == "verification.resend") {
            $hook = "resend_verification_notification";
            $replacement['VERIFICATION_LINK'] = $verificationUrl;
        } else if ($this->getAttribute('password') == null) {
            $hook = "create-new-password";
            $token = app('auth.password.broker')->createToken($this);
            $url = url("/admin/password/create/{$token}/?email=" . (base64_encode($this->getEmailForVerification())));
            $replacement['CREATE_NEW_PASSWORD'] = $url;
        } else {
            $hook = "admin-welcome-email";
            $replacement['verify_n_password'] = $verificationUrl;
        }
        $role_id = $this->role->parent_id ?? "";
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $this->name;
        $replacement['USER_EMAIL'] = $this->getEmailForVerification();
        $data = ['template' => $hook, 'hooksVars' => $replacement];

        Mail::to($this->getEmailForVerification())->send(new \App\Mail\ManuMailer($data));
    }

    /**
     * Send the email verification notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        // dd(\Request::route()->getName());
        $verificationUrl = $this->verificationUrl($this);
        if (\Request::route()->getName() == "admin.verification.resend" || \Request::route()->getName() == "admin.verification.send") {
            $hook = "resend_verification_notification";
            $replacement['VERIFICATION_LINK'] = $verificationUrl;
        } else if (\Request::route()->getName() == "admin.admin-users.store") {
            $hook = "admin-welcome-email";
            $replacement['VERIFY_LINK'] = $verificationUrl;
        } else if ($this->getAttribute('password') == null) {
            $hook = "create-new-password";
            $token = Password::broker('admin_users')->createToken($this);
            $url = url("/admin/password/create/{$token}/?email=" . (base64_encode($this->getEmailForVerification())));
            $replacement['CREATE_NEW_PASSWORD'] = $url;
        }
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $this->name;
        $replacement['USER_EMAIL'] = $this->getEmailForVerification();
        $data = ['template' => $hook, 'hooksVars' => $replacement];
        // dump($data);
        // dd($this->getEmailForVerification());
        Mail::to($this->getEmailForVerification())->send(new \App\Mail\ManuMailer($data));
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admin_users');
    }

    /**
     * Send the change new email notification in update record case.
     *
     * @param  string  $token
     * @return void
     */
    public function sendVerificationEmail()
    {
        if ($this->isDirty('email')) {
            $verificationUrl = $this->verificationUrl($this);
            $hook = "email-verification-notification";
            $replacement['VERIFICATION_LINK'] = $verificationUrl;
            $replacement['USER_NAME'] = $this->name;
            $replacement['USER_EMAIL'] = $this->getEmailForVerification();
            $data = ['template' => $hook, 'hooksVars' => $replacement];
            Mail::to($this->getEmailForVerification())->send(new \App\Mail\ManuMailer($data));
        }
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'admin.verification.verify',
            \Illuminate\Support\Carbon::now()->addMinutes(\Illuminate\Support\Facades\Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
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
                $query->where('first_name', 'LIKE', '%' . $keyword . '%');
                $query->where('last_name', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('email', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status = 0)
    {
        if ($status == 1) {
            $query->where('status', $status);
        }
        return $query;
    }
}
