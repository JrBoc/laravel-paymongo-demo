<?php

namespace App\Models;

use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property string $type
 * @property string $transaction_id
 * @property array $payload
 * @property string|null $src_id
 * @property string|null $pi_id
 * @property string|null $pay_id
 * @property array $initial_response
 * @property array $source_response
 * @property mixed $intent_payment_response
 * @property array $payment_response
 * @property array $re_query_response
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $readable_created_at
 * @property-read mixed $readable_type
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereInitialResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereIntentPaymentResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePaymentResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereReQueryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereSourceResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereSrcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereUserId($value)
 * @mixin \Eloquent
 */
class EWalletPayment extends Model
{
    use HasFactory;

    protected $table = 'app_ewallet_payments';

    protected $appends = [
        'readable_created_at',
        'readable_type',
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

        self::creating(function($model) {
            if(is_null($model->transaction_id)) {
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
