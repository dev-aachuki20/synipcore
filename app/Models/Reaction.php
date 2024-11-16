<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'reactable',
        'user_id',
        'reaction_type',
        'created_at',
        'updated_at',
    ];

    /**
     * Get all of the models that own uploads.
     */
    public function reactable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
