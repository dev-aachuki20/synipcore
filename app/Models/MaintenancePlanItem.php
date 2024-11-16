<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MaintenancePlanItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'maintenance_plan_id',
        'maintenance_item_id',
        'month',
        'budget',
        'comments',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function plan()
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    public function item()
    {
        return $this->belongsTo(MaintenanceItem::class, 'maintenance_item_id');
    }

    public function maintenanceItem()
    {
        return $this->belongsTo(MaintenanceItem::class, 'maintenance_item_id');
    }
}
