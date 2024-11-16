<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',

        'user_id',
        'model_id',
        'amount',

        'transaction_type',
        'transaction_id',
        'stripe_payment_id',

        'payment_data',
        'user_data',
        'model_data',
        'status',

        'created_by',
        'updated_by',

        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_data' => 'array',
            'user_data' => 'array',
            'model_data' => 'array',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Transaction $model) {
            $model->uuid = Str::uuid();

            $model->transaction_id = 'TRX-' . str_pad(Transaction::max('id') + 1, 8, "0", STR_PAD_LEFT);
        });
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function modelData(){
        if($this->transaction_type == 'amenity_booking'){
            return $this->belongsTo(AmenityBooking::class, 'model_id', 'id');
        } else if($this->transaction_type == 'payment_request') {
            return $this->belongsTo(PaymentRequest::class, 'model_id', 'id');
        }
    }

    public function getMetaNameAttribute(){
        if($this->transaction_type == 'amenity_booking'){
            return $this->modelData->amenity ? $this->modelData->amenity->title : '';
        } else if($this->transaction_type == 'payment_request') {
            return $this->modelData ? $this->modelData->title : '';
        }
    }
}
