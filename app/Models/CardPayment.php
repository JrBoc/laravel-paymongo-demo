<?php

namespace App\Models;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CardPayment extends Model
{
    use HasFactory;

    protected $table = 'app_card_payments';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'payment_method_response' => 'array',
        'payment_intent_response' => 'array',
        'payment_attach_response' => 'array',
        're_query_response' => 'array',
    ];

    protected $appends = [
        'readable_created_at',
        'readable_status',
        'readable_amount',
        'last_payment_error',
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

    public function getReadableStatusAttribute()
    {
        $statuses = [
            'awaiting_payment_method' => [
                'color' => 'secondary',
                'text' => 'Awaiting Payment Method',
                'status' => $this->status,
            ],
            'chargeable' => [
                'color' => 'info',
                'text' => 'Chargeable',
                'status' => $this->status,
            ],
            'succeeded' => [
                'color' => 'success',
                'text' => 'Success',
                'status' => $this->status,
            ],
            'fail' => [
                'color' => 'danger',
                'text' => 'Failed',
                'status' => $this->status,
            ],
            'expired' => [
                'color' => 'dark',
                'text' => 'Expired',
                'status' => 'expired',
            ],
            'awaiting_next_action' => [
                'color' => 'info',
                'text' => 'Awaiting Next Action',
                'status' => $this->status,
            ]
        ];

        if (in_array($this->status, ['awaiting_payment_method', 'pending']) && now()->isAfter($this->created_at->addHour())) {
            return collect($statuses['expired']);
        }

        return collect($statuses[$this->status] ?? [
            'color' => 'warning',
            'text' => Str::title($this->status),
            'status' => $this->status,
        ]);
    }

    public function getLastPaymentErrorAttribute()
    {
        $errors = [];

        if(!is_null($this->re_query_response)) {
            $errors = $this->re_query_response['last_payment_error'];
        }

        return collect($errors);
    }

    public function getReadableCreatedAtAttribute()
    {
        if($this->created_at) {
            return $this->created_at->toPhFormat();
        }

        return $this->created_at;
    }

    public function getReadableAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function isAuthenticatable(): bool
    {
        return in_array($this->readable_status['status'], [
            'awaiting_next_action'
        ]);
    }

    public function isPayable(): bool
    {
        return $this->status == 'awaiting_payment_method' && !now()->isAfter($this->created_at->addHour());
    }
}
