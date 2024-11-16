<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'visitor_logsable',
        'status',
        'created_at',
        'updated_at',
    ];
    /**
     * Get all of the models that own uploads.
     */
    public function visitorLogsable()
    {
        return $this->morphTo();
    }
}
