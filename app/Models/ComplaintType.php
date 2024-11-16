<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ComplaintType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'sort_order',
        'updated_by',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ComplaintType $model) {
            $model->uuid = Str::uuid();
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

    public function complaintImage()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'complaint_image');
    }

    public function getComplaintImageUrlAttribute()
    {
        // $complaintImage = [];
        // if ($this->complaintImage) {
        //     foreach ($this->complaintImage()->get() as $complaintImage) {
        //         $complaintImage[] = $complaintImage->file_url;
        //     }
        //     return $complaintImage;
        // }
        // return "";

        if ($this->complaintImage) {
            return $this->complaintImage->file_url;
        }
        return "";
    }
}
