<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Visitor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'user_id',
        'name',
        'phone_number',
        'cab_number',
        'visit_date',
        'visitor_type',
        'visitor_note',
        'gatepass_code',
        'qr_code',
        'other_info',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Visitor $model) {
            $model->uuid = Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime'
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id', 'id');
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function visitorQr()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'visitor_gatepass_qr');
    }

    public function getGatepassQrImageAttribute()
    {
        if ($this->visitorQr) {
            return $this->visitorQr->file_url;
        }
        return "";
    }

    public function visitorLogs()
    {
        return $this->morphMany(VisitorLog::class, 'visitor_Logsable');
    }
}
