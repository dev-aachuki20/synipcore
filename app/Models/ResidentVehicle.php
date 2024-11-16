<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ResidentVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'resident_id',
        'vehicle_number',
        'vehicle_type',
        'vehicle_model',
        'vehicle_color',
        'parking_slot_no',
        'society_id',
        'building_id',
        'unit_id',
        'gatepass_code',
        'qr_code',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ResidentVehicle $model) {
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

    public function residents()
    {
        return $this->hasMany(User::class, 'resident_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'resident_id', 'id');
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function vehicleImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'resident_vehicle_image');
    }

    public function getVehicleImageUrlAttribute()
    {
        if ($this->vehicleImage) {
            return $this->vehicleImage->file_url;
        }
        return "";
    }

    public function vehicleQr()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'vehicle_gatepass_qr');
    }

    public function getGatepassQrImageAttribute()
    {
        if ($this->vehicleQr) {
            return $this->vehicleQr->file_url;
        }
        return "";
    }
}
