<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Amenity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'title',
        'description',
        'fee_type',
        'fee',
        'capacity',
        'booking_capacity',
        'advance_booking_days',
        'society_id',

        'max_days_per_unit',

        'created_by',
        'created_by',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Amenity $model) {
            $model->uuid = Str::uuid();

            $model->created_by = auth()->user() ? auth()->user()->id : null;
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function amenityBookings()
    {
        return $this->hasMany(AmenityBooking::class, 'amenity_id', 'id');
    }
}
