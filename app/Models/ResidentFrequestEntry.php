<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ResidentFrequestEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "resident_frequest_entries";

    protected $fillable = [
        'uuid',
        'resident_id',
        'name',
        'phone_number',
        'task',
        'gatepass_code',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ResidentFrequestEntry $model) {
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
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'frequest_entry_profile_image');
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profileImage) {
            return $this->profileImage->file_url;
        }
        return "";
    }

    public function frequestEntryQr()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'frequest_entry_qr');
    }

    public function getGatepassQrImageAttribute()
    {
        if ($this->frequestEntryQr) {
            return $this->frequestEntryQr->file_url;
        }
        return "";
    }
}
