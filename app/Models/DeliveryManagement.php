<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryManagement extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "delivery_management";

    protected $fillable = [
        'uuid',
        'subject',
        'message',
        'notes',
        'delivery_type_id',
        'society_id',
        'building_id',
        'unit_id',
        'status',
        'respondant_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function type()
    {
        return $this->belongsTo(DeliveryType::class, 'delivery_type_id', 'id');
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

    public function user()
    {
        return $this->belongsTo(User::class, 'respondant_id', 'id');
    }
}
