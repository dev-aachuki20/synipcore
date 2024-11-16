<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AmenityBooking extends Model
{
    use SoftDeletes;

    public $table = 'amenity_bookings';

    protected $fillable = [
        'uuid',

        'user_id',
        'amenity_id',

        'from_date',
        'from_time',

        'to_date',
        'to_time',
        'amount',
        'max_users_capacity',

        'status',
        'payment_status',
        'is_full_day',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'from_time' => 'datetime:H:i:s',
            'to_date'   => 'datetime:H:i:s',
            'to_time'   => 'datetime:H:i:s',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (AmenityBooking $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'id');
    }

    public function amenityBookingTransactions()
    {
        return $this->HasMany(Transaction::class, 'model_id', 'id')->where('transaction_type', 'amenity_booking');
    }

    public function getDatePeriodAttribute()
    {
        return \Carbon\Carbon::parse($this->from_date)->format(config('constant.date_format.date')) . ' - ' . \Carbon\Carbon::parse($this->to_date)->format(config('constant.date_format.date'));
    }
}
