<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaField extends Model
{
    use HasFactory;

    protected $table = "meta_keys";
    protected $fillable = [
        'metaable',
        'key',
        'value',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get all of the owning metaable models.
     */
    public function metaable()
    {
        return $this->morphTo();
    }
}
