<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "posts";

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'slug',
        'content',
        'video_url',
        'post_type',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid();
            $model->created_by = auth()->user() ? auth()->user()->id : null;
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->user() ? auth()->user()->id : null;
        });
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function postImages()
    {
        return $this->morphMany(Uploads::class, 'uploadsable')->where('media_type', 'post_image');
    }

    public function getPostImageUrlsAttribute()
    {
        $postImages = [];
        if ($this->postImages) {
            foreach($this->postImages()->get() as $postImage){
                $postImages[] = $postImage->file_url;
            }
            return $postImages;
        }
        return "";
    }

    public function postVideo()
    {
        return $this->morphOne(Uploads::class, 'uploadsable')->where('media_type', 'post_video');
    }

    public function getPostVideoUrlAttribute()
    {
        $postVideo = '';
        if ($this->postVideo) {
            $postVideo = $this->postVideo->file_url;
        }
        return $postVideo;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('is_approve', 1);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likes()
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('reaction_type', 'like');
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function getTotalLikesAttribute()
    {
        return $this->likes()->count();
    }

    public function getTotalCommentsAttribute()
    {
        return $this->comments()->count();
    }

    public function getTotalApprovedCommentsAttribute()
    {
        return $this->comments()->where('is_approve', 1)->count();
    }

    public function getTotalViewsAttribute()
    {
        return $this->views()->count();
    }
}
