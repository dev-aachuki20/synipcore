<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "units";
    protected $fillable = [
        'uuid',
        'title',
        'society_id',
        'building_id',
        'status',
        'updated_by',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $uuid = Str::uuid(16);
            $model->uuid = $uuid/* . '-' . now()->timestamp */;
            $model->created_by = auth()->user() ? auth()->user()->id : 1;
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

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function residents()
    {
        return $this->hasMany(User::class, 'unit_id', 'id');
    }
}
