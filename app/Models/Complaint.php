<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'user_id',
        'complaint_type_id',
        'category',
        'description',
        'status',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Complaint $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function complaintImages()
    {
        return $this->morphMany(Uploads::class, 'uploadsable')->where('media_type', 'complaint_image');
    }

    public function getComplaintImageUrlsAttribute()
    {
        $complaintImages = [];
        if ($this->complaintImages) {
            foreach ($this->complaintImages()->get() as $complaintImage) {
                $complaintImages[] = $complaintImage->file_url;
            }
            return $complaintImages;
        }
        return "";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function complaintType()
    {
        return $this->belongsTo(ComplaintType::class, 'complaint_type_id', 'id');
    }

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getTotalCommentsAttribute()
    {
        return $this->comments()->count();
    }

    public function getTotalApprovedCommentsAttribute()
    {
        return $this->comments()->where('is_approve', 1)->count();
    }
}
