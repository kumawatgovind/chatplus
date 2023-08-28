<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Kyslik\ColumnSortable\Sortable;
use Mail, DB;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Sortable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        "parent_id",
        "referral_code",
        "name",
        "email",
        "username",
        "country_code",
        "phone_number",
        "profile_image",
        "cover_image",
        "bio",
        "dob",
        "janam_din",
        "gender",
        "marital_status",
        "is_block",
        "email_verified_at",
        "password",
        'two_factor_secret',
        'two_factor_recovery_codes',
        "remember_token",
        "device_id",
        "device_type",
        "api_token",
        "verification_code",
        "status",
        "device_id",
        "device_type",
        "api_token",
        "firebase_id",
        "firebase_email",
        "firebase_password",
        "created_at",
        "updated_at",
    ];


    /**
     * The sortable used for column sort.
     *
     * @var array
     */
    public $sortable = ["first_name", "last_name", "email", 'status', 'email_verified_at', 'created_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'updated_at',
        'created_at',
        'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'profile_base_url',
        'registered_date',
        'profile_last_updated_date',
        'country_phone_number'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::saved(function ($model) {
            // $model->sendRegEmail($model);
        });
    }

    /**
     * Update profile_created Columns.
     *
     * @var string
     */
    public function getProfileBaseUrlAttribute()
    {
        return asset('storage/profile/');
        // return !empty($this->profile_image) ? $this->profile_image : "";
    }

    /**
     * getRegisteredDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getRegisteredDateAttribute($value)
    {
        return !empty($this->created_at) ? strtotime($this->created_at) : "";
    }

    /**
     * getProfileLastUpdatedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getProfileLastUpdatedDateAttribute($value)
    {
        return !empty($this->updated_at) ? strtotime($this->updated_at) : "";
    }


    /**
     * getProfileLastUpdatedDateAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getCountryPhoneNumberAttribute($value)
    {
        $phoneNumber = '';
        if (!empty($this->country_code) || !empty($this->phone_number)) {
            $phoneNumber = $this->country_code . $this->phone_number;
        }
        return $phoneNumber;
    }


    /**
     * main_profile
     *
     * @return void
     */
    public function main_profile()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * userServices
     *
     * @return void
     */
    public function userServicesProfile()
    {
        return $this->hasOne(ServiceProfile::class, 'user_id', 'id');
    }

    /**
     * serviceProduct
     *
     * @return void
     */
    public function serviceProduct()
    {
        return $this->hasMany(ServiceProduct::class, 'user_id', 'id');
    }

    /**
     * userSubscribe
     *
     * @return void
     */
    public function userSubscribe()
    {
        return $this->hasMany(Subscription::class, 'user_id', 'id');
    }

    /**
     * userSponsor
     *
     * @return void
     */
    public function userSponsor()
    {
        return $this->hasMany(Sponsor::class, 'sponsor_user_id', 'id');
    }

    /**
     * userSponsored
     *
     * @return void
     */
    public function userSponsored()
    {
        return $this->hasMany(Sponsor::class, 'sponsored_user_id', 'id');
    }

    /**
     * userEarnings
     *
     * @return void
     */
    public function userEarnings()
    {
        return $this->hasMany(UserEarning::class, 'user_id', 'id');
    }

    /**
     * userSubscription
     *
     * @return void
     */
    public function userSubscription()
    {
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')->where('is_active', '=', 1);
    }

    /**
     * activeSubscription
     *
     * @return void
     */
    public function activeSubscription()
    {
        $date = date('Y-m-d H:i:s');
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')
        ->where('start_date', '<=', $date)
        ->where('end_date', '>=', $date)
        ->where('is_active', 1);
    }

    /**
     * userStatus
     *
     * @return void
     */
    public function userStatus()
    {
        return $this->hasMany(UserStatus::class, 'user_id', 'id');
    }

    /**
     * kycDocument
     *
     * @return void
     */
    public function kycDocument()
    {
        return $this->hasMany(KycDocument::class, 'user_id', 'id');
    }

    /**
     * contactSync
     *
     * @return void
     */
    public function contactSync()
    {
        return $this->hasMany(ContactSync::class, 'user_id', 'id');
    }

    /**
     * reportedUser
     *
     * @return void
     */
    public function reportedUser()
    {
        return $this->hasOne(ReportedSpam::class, 'item_id', 'id')->where('type', '=', 1);
    }

    /**
     * userProduct
     *
     * @return void
     */
    public function userProduct()
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }

    /**
     * userCustomer
     *
     * @return void
     */
    public function userCustomer()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }

    /**
     * reportedByUser
     *
     * @return void
     */
    public function reportedByUser()
    {
        return $this->belongsTo(ReportedSpam::class, 'reported_by');
    }


    /**
     * userServiceProductBookmark
     *
     * @return void
     */
    public function userServiceProductBookmark()
    {
        return $this->belongsToMany('App\Models\ServiceProduct', 'service_product_bookmark', 'user_id', 'service_product_id')->withTimeStamps();
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
                $query->where('users.name', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('users.email', 'LIKE', '%' . $keyword . '%');
                $query->orWhere('users.phone_number', 'LIKE', '%' . $keyword . '%');
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
        return $query->where('status', $status);
    }

    /**
     * scopeBlock
     *
     * @param  mixed $query
     * @param  mixed $status
     * @return void
     */
    public function scopeBlock($query, $status = 1)
    {
        return $query->where('is_block', $status);
    }


    public function sendRegEmail($model)
    {
        if (\Request::route()->getName() == "admin.users.store") {
            // dd($model);
            // dd(request('random'));
            if ($model->getAttribute('email') && $model->getOriginal('email') != $model->getAttribute('email')) {

                $token = app('auth.password.broker')->createToken($model);
                $replacement['token'] = $token;
                $userData = '';
                $userData .= "Email: " . $model->getAttribute('email') . "<br>";
                $userData .= "Password: " . request('random');
                // $userData .= '<p> <strong> Please note it may take up to 24 hours for your account to be activated. You will be sent a confirmation email once you’re able to log in. Thanks for your patience, we really appreciate it, Please click on below link to email verification </strong> <p>';
                // $replacement['VERIFY_LINK'] = url('/users/verify-account/' . $model->getAttribute('id') . '/' . $token);
                // $replacement['USER_NAME'] = $model->getAttribute('first_name') . ' ' . $model->getAttribute('last_name');
                DB::table('user_verifications')->insert([
                    'user_id' => $model->getAttribute('id'),
                    'verification_code' => $token, //change 60 to any length you want
                    'type' => 1,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
                $replacement['USER_INFO'] = $userData;
                $replacement['USER_NAME'] = $model->getAttribute('first_name') . ' ' . $model->getAttribute('last_name');
                $replacement['USER_EMAIL'] = $model->getAttribute('email');
                $hook = "welcome_email_for_user_by_admin";
                $data = ['template' => $hook, 'hooksVars' => $replacement];

                Mail::to($model->getAttribute('email'))->send(new \App\Mail\ManuMailer($data));
            }
        } else {
            if ($model->getAttribute('email') && $model->getOriginal('email') != $model->getAttribute('email')) {

                $token = app('auth.password.broker')->createToken($model);
                $verificationUrl = $this->verificationUrl($this);
                $replacement['token'] = $token;
                $userData = '';
                $userData .= "User Name: " . $model->getAttribute('email') . "<br>";
                $userData .= '<p> <strong> Please note it may take up to 24 hours for your account to be activated. You will be sent a confirmation email once you’re able to log in. Thanks for your patience, we really appreciate it, Please click on below link to email verification </strong> <p>';

                $hook = "verification-link";
                $replacement['VERIFY_LINK'] = $verificationUrl;
                $replacement['USER_NAME'] = $model->getAttribute('first_name') . ' ' . $model->getAttribute('last_name');

                $replacement['USER_INFO'] = $userData;
                $replacement['USER_NAME'] = $model->getAttribute('first_name') . ' ' . $model->getAttribute('last_name');
                $replacement['USER_EMAIL'] = $model->getAttribute('email');
                $data = ['template' => $hook, 'hooksVars' => $replacement];
                Mail::to($model->getAttribute('email'))->send(new \App\Mail\ManuMailer($data));
            } elseif (\Request::route()->getName() == "frontend.forgot-password.email") {

                $token = app('auth.password.broker')->createToken($model);
                $userData = '';
                $replacement['token'] = $token;
                $hook = "create-new-password";
                $url = url("/reset-password/{$token}") . '?email=' . $model->email;
                $replacement['CREATE_NEW_PASSWORD'] = $url;
                $replacement['USER_INFO'] = $userData;
                $replacement['USER_NAME'] = $model->getAttribute('first_name') . ' ' . $model->getAttribute('last_name');
                $replacement['USER_EMAIL'] = $model->getAttribute('email');
                $data = ['template' => $hook, 'hooksVars' => $replacement];
                Mail::to($model->getAttribute('email'))->send(new \App\Mail\ManuMailer($data));
            }
        }
    }

    public function sendApprovedEmail($model)
    {
        $hook = "after-approve-advertiser-welcome-email";
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $model->getAttribute('first_name');
        $replacement['USER_EMAIL'] = $model->getAttribute('email');
        $data = ['template' => $hook, 'hooksVars' => $replacement];
        Mail::to($model->getAttribute('email'))->send(new \App\Mail\ManuMailer($data));
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
            $replacement['RESET_PASSWORD_URL'] = url("/reset-password/{$token}/?email=" . (base64_encode(request('email'))));
        } else if (\Request::route()->getName() == "verification.resend") {
            $hook = "resend_verification_notification";
            $replacement['VERIFICATION_LINK'] = $verificationUrl;
        } else if ($this->getAttribute('password') == null) {
            $hook = "create-new-password";
            $token = app('auth.password.broker')->createToken($this);
            $url = url("/password/create/{$token}/?email=" . (base64_encode($this->getEmailForVerification())));
            $replacement['CREATE_NEW_PASSWORD'] = $url;
        }
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $this->first_name;
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

        $verificationUrl = $this->verificationUrl($this);
        $hook = "";
        if (\Request::route()->getName() == "frontend.verification.send" || \Request::route()->getName() == "admin.verification.send") {
            $hook = "resend_verification_notification";
            $replacement['VERIFICATION_LINK'] = $verificationUrl;
        } else if (\Request::route()->getName() == "admin.admin-users.store") {
            // $hook = "welcome-email";
            $hook = "verification-link";
            $replacement['VERIFY_LINK'] = $verificationUrl;
        } else if ($this->getAttribute('password') == null) {
            $hook = "create-new-password";
            $token = Password::broker('admin_users')->createToken($this);
            $url = url("/password/create/{$token}/?email=" . (base64_encode($this->getEmailForVerification())));
            $replacement['CREATE_NEW_PASSWORD'] = $url;
        }
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $this->first_name;
        $replacement['USER_EMAIL'] = $this->getEmailForVerification();
        if (!empty($hook)) {
            $data = ['template' => $hook, 'hooksVars' => $replacement];
            // dump($data);
            // dd($this->getEmailForVerification());
            //Mail::to($this->getEmailForVerification())->send(new \App\Mail\ManuMailer($data));
            return false;
        }
    }


    /**
     * Send the email verification notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendWelcomeMail()
    {

        $hook = "welcome-email";
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $this->first_name;
        $replacement['USER_EMAIL'] = $this->getEmailForVerification();
        $data = ['template' => $hook, 'hooksVars' => $replacement];
        // dd($data);
        // dd($this->getEmailForVerification());
        Mail::to($this->getEmailForVerification())->send(new \App\Mail\ManuMailer($data));
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
            'frontend.verification.verify',
            \Illuminate\Support\Carbon::now()->addMinutes(\Illuminate\Support\Facades\Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Send the email verification notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendReverificationMail($requestData)
    {
        $verificationUrl = $this->verificationUrl($this);
        $hook = "resend_verification_on_change_email";
        $replacement['USER_INFO'] = '';
        $replacement['USER_NAME'] = $requestData['first_name'];
        $replacement['USER_EMAIL'] = $requestData['email'];
        $replacement['USER_OLD_EMAIL'] = $this->email;
        $replacement['VERIFY_LINK'] = $verificationUrl;
        $data = ['template' => $hook, 'hooksVars' => $replacement];

        Mail::to($requestData['email'])->send(new \App\Mail\ManuMailer($data));
    }
}
