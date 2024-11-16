<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use SoftDeletes;

    protected $guard = 'web';

    public $table = 'service_bookings';

    protected $fillable = [
        'uuid',
        'service_id',
        'user_id',
        'booking_date',
        'booking_time',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date:Y-m-d',
            'booking_time' => 'datetime:H:i:s',
        ];
    }


    protected static function boot()
    {
        parent::boot();
        static::creating(function (ServiceBooking $model) {
            $model->uuid = Str::uuid();
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
