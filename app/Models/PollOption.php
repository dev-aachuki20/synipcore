<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "poll_options";

    protected $fillable = [
        'notice_board_id',
        'option',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function announcements()
    {
        return $this->belongsTo(Announcement::class, 'notice_board_id', 'id');
    }

    public function pollVotes(){
        return $this->hasMany(PollVote::class, 'poll_option_id', 'id');
    }


}
