<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "services";

    protected $fillable = [
        'uuid',
        'user_id',
        'service_category_id',
        'title',
        'slug',
        'description',
        'sort_order',
        'service_url',
        'is_featured',
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
            $model->created_by = auth()->user() ? auth()->user()->id : null;
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function serviceImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'service_image');
    }

    public function getServiceImageUrlAttribute()
    {
        if ($this->serviceImage) {
            return $this->serviceImage->file_url;
        }
        return "";
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_service');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
