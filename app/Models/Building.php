<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "buildings";
    protected $fillable = [
        'uuid',
        'title',
        'society_id',
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

}
