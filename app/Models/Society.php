<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Society extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "societies";
    protected $fillable = [
        'uuid',
        'name', // title
        'address',
        'district',
        'fire_alert',
        'lift_alert',
        'animal_alert',
        'visitor_alert',
        'latitude',
        'longitude',
        'city',
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

    public function metafields()
    {
        return $this->morphMany(MetaField::class, 'metaable');
    }

    public function metafieldKeysValue()
    {
        return $this->morphMany(MetaField::class, 'metaable')->where('metaable_type', self::class);
    }

    public function societyCity()
    {
        return $this->belongsTo(Location::class, 'city', 'id');
    }

    public function societyDistrict()
    {
        return $this->belongsTo(Location::class, 'district', 'id');
    }

    public function buildings()
    {
        return $this->hasMany(Building::class, 'society_id', 'id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'society_id', 'id');
    }

    public function societyAdmins()
    {
        return $this->hasMany(User::class, 'society_id', 'id')->whereHas('roles', function ($q) {
            $q->where('id', config('constant.roles.admin'));
        });
    }

    public function users()
    {
        return $this->hasMany(User::class, 'society_id');
    }

    public function userslist()
    {
        return $this->belongsToMany(User::class, 'provider_societies');
    }
}
