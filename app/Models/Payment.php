<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'app_payments';

    protected $appends = [
        'readable_created_at',
        'readable_type',
    ];

    protected $casts = [
        'payload' => 'array',
        'intent_response' => 'array',
        'initial_response' => 'array',
        'source_response' => 'array',
        'payment_response' => 'array',
        're_query_response' => 'array',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatus()
    {
        if (in_array($this->status, ['chargeable', 'pending'])) {
            if (Carbon::now()->isAfter($this->created_at->addHour())) {
                return [
                    'color' => 'dark',
                    'text' => 'Expired',
                ];
            } else {
                return  [
                    'pending' => [
                        'color' => 'secondary',
                        'text' => 'Pending',
                    ], 'chargeable' => [
                        'color' => 'info',
                        'text' => 'Chargeable',
                    ],
                ][$this->status] ?? [
                    'color' => 'dark',
                    'text' => $this->status,
                ];
            }
        }

        return [
            'pending' => [
                'color' => 'secondary',
                'text' => 'Pending',
            ],
            'paid' => [
                'color' => 'success',
                'text' => 'Success',
            ],
            'failed' => [
                'color' => 'danger',
                'text' => 'Failed',
            ],
            'expired' => [
                'color' => 'dark',
                'text' => 'Expired',
            ]
        ][$this->status] ?? [
            'color' => 'dark',
            'text' => $this->status,
        ];
    }

    public function getReadableTypeAttribute()
    {
        return [
            'gcash' => 'GCash',
            'grab_pay' => 'GrabPay',
        ][$this->type] ?? $this->type;
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->addHours(8)->format('D, M j, Y, g:i A');
    }
}
