<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SecurityAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "security_alerts";

    protected $fillable = [
        'resident_id',
        'alert_type',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function resident(){
        return $this->belongsTo(User::class, 'resident_id', 'id');
    }
}
