<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'announcement_type',
        'title',
        'message',
        'posted_by',
        'society_id',
        'status',
        'created_by',
        'updated_by',
        'expire_date',
        'poll_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'expire_date' => 'datetime',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $uuid = Str::uuid(16);
            $model->uuid = $uuid;
            $model->created_by = auth()->user() ? auth()->user()->id : 1;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user() ? auth()->user()->id : null;
        });
    }


    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likes()
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('reaction_type', 'like');
    }

    public function dislikes()
    {
        return $this->morphMany(Reaction::class, 'reactable')->where('reaction_type', 'dislike');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public function options()
    {
        return $this->hasMany(PollOption::class, 'notice_board_id', 'id');
    }

    public function pollVotes()
    {
        return $this->hasMany(PollVote::class, 'notice_board_id', 'id');
    }

    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('is_approve', 1);
    }

    public function getTotalApprovedCommentsAttribute()
    {
        return $this->comments()->where('is_approve', 1)->count();
    }

    public function getTotalCommentsAttribute()
    {
        return $this->comments()->count();
    }

    public function uploads()
    {
        return $this->morphMany(Uploads::class, 'uploadsable');
    }

    public function AnnouncementImages()
    {
        return $this->morphMany(Uploads::class, 'uploadsable')->where('media_type', 'announcement_image');
    }

    public function getAnnouncementImageUrlsAttribute()
    {
        $AnnouncementImages = [];
        if ($this->AnnouncementImages) {
            foreach ($this->AnnouncementImages()->get() as $announcementImage) {
                $AnnouncementImages[] = $announcementImage->file_url;
            }
            return $AnnouncementImages;
        }
        return "";
    }
}
