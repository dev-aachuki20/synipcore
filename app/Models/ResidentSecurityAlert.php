<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class ResidentSecurityAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "resident_security_alerts";

    protected $fillable = [
        'uuid',
        'resident_id',
        'name',
        'phone_number',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ResidentSecurityAlert $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function resident()
    {
        return $this->belongsTo(User::class, 'resident_id', 'id');
    }    
}
