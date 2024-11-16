<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MaintenancePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'society_id',
        'year_of',
        // 'category_id',
        'maintenance_item_id',
        'month',
        'total_budget',
        'comments',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'month' => 'array', // Casting month as an array
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function (MaintenancePlan $model) {
            $model->uuid = Str::uuid();

            $model->created_by = auth()->user() ? auth()->user()->id : null;
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function maintenance_item()
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    public function items()
    {
        return $this->hasMany(MaintenancePlanItem::class);
    }
}
