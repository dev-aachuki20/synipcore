<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'guard_id',
        'message',
        'status',
        'read_at',
        'created_at',
        'updated_at',
    ];


    public function resident(){
        return $this->belongsTo(User::class, 'resident_id', 'id');
    }

    public function guardUser(){
        return $this->belongsTo(User::class, 'guard_id', 'id');
    }
}
