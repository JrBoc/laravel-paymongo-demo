<?php

namespace App\Models;

use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EWalletPayment extends Model
{
    use HasFactory;

    protected $table = 'app_ewallet_payments';

    protected $appends = [
        'readable_created_at',
        'readable_type',
        'readable_status',
        'readable_amount',
    ];

    protected $casts = [
        'payload' => 'array',
        'initial_response' => 'array',
        'source_response' => 'array',
        'payment_response' => 'array',
        're_query_response' => 'array',
    ];

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (is_null($model->transaction_id)) {
                $model->transaction_id = IdGenerator::generate([
                    'table' => $model->table,
                    'field' => 'transaction_id',
                    'length' => 16,
                    'prefix' => 'PM-WALLET-',
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
            'pending' => [
                'color' => 'secondary',
                'text' => 'Pending',
                'status' => $this->status,
            ],
            'chargeable' => [
                'color' => 'info',
                'text' => 'Chargeable',
                'status' => $this->status,
            ],
            'paid' => [
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
            ]
        ];

        if (in_array($this->status, ['chargeable', 'pending']) && now()->isAfter($this->created_at->addHour())) {
            return collect($statuses['expired']);
        }

        return collect($statuses[$this->status] ?? [
            'color' => 'warning',
            'text' => Str::title($this->status),
            'status' => $this->status,
        ]);
    }

    public function getReadableTypeAttribute()
    {
        return [
            'gcash' => 'GCash',
            'grab_pay' => 'GrabPay',
        ][$this->type];
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->addHours(8)->format('D, M j, Y, g:i A');
    }

    public function getReadableAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function isReQueryable(): bool
    {
        return in_array($this->readable_status['status'], [
            'chargeable',
        ]);
    }

    public function isPayable(): bool
    {
        return $this->status == 'pending' && !now()->isAfter($this->created_at->addHour());
    }
}
