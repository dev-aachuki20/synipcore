<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ResidentDailyHelp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'resident_id',
        'name',
        'phone_number',
        'help_type',
        'society_id',
        'building_id',
        'unit_id',
        'gatepass_code',
        'qr_code',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ResidentDailyHelp $model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'daily_help_profile_image');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function dailyHelpQr()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'daily_help_gatepass_qr');
    }

    public function getGatepassQrImageAttribute()
    {
        if ($this->dailyHelpQr) {
            return $this->dailyHelpQr->file_url;
        }
        return "";
    }
}
