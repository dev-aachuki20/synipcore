<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use SoftDeletes;

    protected $table = "poll_votes";

    protected $fillable = [
        'notice_board_id',
        'poll_option_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function noticeBoard()
    {
        return $this->belongsTo(Announcement::class, 'notice_board_id', 'id');
    }

    public function pollOption()
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
