<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
   
    public $table = 'permissions';

    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [
        'name',
        'title',
        // 'guard_name',
        'route_name',
        // 'type',
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_has_permission');
    }
}
