<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Support extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'user_id',
        'topic',
        'message',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (Support $model) {
            $model->uuid = Str::uuid();
        });
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
