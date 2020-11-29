<?php

namespace App\Models;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardPayment extends Model
{
    use HasFactory;

    protected $table = 'app_card_payments';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'payment_intent_response' => 'array',
        'payment_attach_response' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model) {
            if(is_null($model->transaction_id)) {
                $model->transaction_id = IdGenerator::generate([
                    'table' => $model->table,
                    'field' => 'transaction_id',
                    'length' => 14,
                    'prefix' => 'PM-CARD-',
                ]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->addHours(8)->format('D, M j, Y, g:i A');
    }
}
