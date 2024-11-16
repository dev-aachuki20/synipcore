<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ResidentFamilyMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'resident_id',
        'name',
        'phone_number',
        'relation',
        'gatepass_code',
        'qr_code',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ResidentFamilyMember $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id', 'id');
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function profileImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'resident_family_member_profile_image');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function familyMemberQr()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'resident_family_member_qr');
    }

    public function getGatepassQrImageAttribute()
    {
        if ($this->familyMemberQr) {
            return $this->familyMemberQr->file_url;
        }
        return "";
    }
}
