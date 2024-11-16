<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Unit;
use App\Models\Building;
use App\Models\Society;

class AiBoxAlert extends Model
{
    use SoftDeletes;

    public $table = 'ai_box_alerts';

    protected $fillable = [
        'society_id',
        'building_id',
        'unit_id',
        'camera_id',
        'notification_data',
        'api_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'notification_data' => 'array'
        ];
    }


    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id', 'id');
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
