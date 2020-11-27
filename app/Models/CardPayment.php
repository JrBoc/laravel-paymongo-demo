<?php

namespace App\Models;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CardPayment
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property string $transaction_id
 * @property array|null $payload
 * @property string|null $pm_id
 * @property string|null $pi_id
 * @property int $payment_attached
 * @property array|null $initial_response
 * @property mixed|null $payment_method_response
 * @property mixed|null $payment_intent_response
 * @property mixed|null $re_query_response
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereInitialResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePaymentAttached($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePaymentIntentResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePaymentMethodResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment wherePmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereReQueryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereUserId($value)
 * @mixin \Eloquent
 */
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
