<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "locations";

    protected $fillable = [
        'uuid',
        'title',
        'scope_id',
        'slug',
        'sort_order',
        'parent_id',
        // 'meta_field_id',
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
            $model->updated_by = auth()->user() ? auth()->user()->id : 1;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : 1;
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function locationImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'location_image');
    }

    public function getLocationImageUrlAttribute()
    {
        if ($this->locationImage) {
            return $this->locationImage->file_url;
        }
        return "";
    }

    public function metafields()
    {
        return $this->morphMany(MetaField::class, 'metaable');
    }

    public function metafieldKeysValue()
    {
        // return $this->morphMany(MetaField::class, 'metaable')->where('metaable_type', 'App\Models\Location');
        return $this->morphMany(MetaField::class, 'metaable')->where('metaable_type', self::class);
    }

    public function parentLocation(){
        return $this->belongsTo(Location::class, 'parent_id', 'id');
    }
}
