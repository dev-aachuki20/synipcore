<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "property_management";

    protected $fillable = [
        'uuid',
        'property_item',
        'property_type_id',
        'society_id',
        'building_id',
        'unit_id',
        'property_code',
        'description',
        'amount',
        'unit_price',
        'purchase_date',
        'location',
        'allocation',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $dates = ['purchase_date'];

    protected $casts = [
        'purchase_date' => 'datetime',
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

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function propertyManagementimages()
    {
        return $this->morphMany(Uploads::class, 'uploadsable')->where('media_type', 'property_image');
    }

    public function getPropertyManagementimagesImageUrlsAttribute()
    {
        $propertyManagementimages = [];
        if ($this->propertyManagementimages) {
            foreach ($this->propertyManagementimages()->get() as $propertyManagementimage) {
                $propertyManagementimages[] = $propertyManagementimage->file_url;
            }
            return $propertyManagementimages;
        }
        return "";
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

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function maintenanceItem()
    {
        return $this->belongsTo(MaintenanceItem::class);
    }
}
