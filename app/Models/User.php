<?php

namespace App\Models;

use App\Mail\SendResetPasswordOtpMail;
use App\Notifications\OtpSendNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable, HasApiTokens;

    protected $guard = 'web';

    public $table = 'users';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'security_pin',
        'guard_duty_status',
        'username',
        'password',
        'mobile_number',
        'description',
        'mobile_verified',
        'is_verified',
        'is_featured',
        'is_enabled',
        'provider_url',
        'status',
        'language_id',
        'location_id',
        'district_id',
        'society_id',
        'building_id',
        'unit_id',
        'social_user_id',
        'login_type',
        'resident_type',
        'device_token',
        'current_session_id',
        'last_login_at',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(function (User $model) {
            $model->uuid = Str::uuid();

            $model->created_by = Auth::user() ? Auth::user()->id : null;
        });
    }

    public function sendPasswordResetOtpNotification($user, $token, $subject, $expiretime)
    {
        // $this->notify(new OtpSendNotification($user,$token, $subject , $expiretime));
        Mail::to($user->email)->send(new SendResetPasswordOtpMail($user, $token, $subject, $expiretime));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.superadmin'))->exists();
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'user_profile');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function city()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(Location::class, 'district_id', 'id');
    }


    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function bookedServices()
    {
        return $this->hasMany(ServiceBooking::class, 'user_id', 'id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'user_id', 'id');
    }

    public function bookedAmenities()
    {
        return $this->hasMany(AmenityBooking::class, 'user_id', 'id');
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'user_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_user', 'user_id', 'feature_id');
    }

    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function dailyhelps()
    {
        return $this->hasMany(ResidentDailyHelp::class, 'resident_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(ResidentVehicle::class, 'resident_id', 'id');
    }

    public function frequestEntries()
    {
        return $this->hasMany(ResidentFrequestEntry::class, 'resident_id', 'id');
    }

    public function familymembers()
    {
        return $this->hasMany(ResidentFamilyMember::class, 'resident_id', 'id');
    }

    public function securityAlertContacts()
    {
        return $this->hasMany(ResidentSecurityAlert::class, 'resident_id', 'id');
    }

    public function guardMessages()
    {
        return $this->hasMany(GuardMessage::class, 'guard_id', 'id');
    }

    public function userLanguage()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function getIsSubAdminAttribute()
    {
        return $this->roles()->where('id', config('constant.roles.admin'))->exists();
    }

    public function societies()
    {
        return $this->belongsToMany(Society::class, 'provider_societies');
    }
}
