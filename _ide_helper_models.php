<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
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
 * @property array|null $payment_method_response
 * @property array|null $payment_intent_response
 * @property mixed|null $re_query_response
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $readable_created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CardPayment whereId($value)
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
 */
	class CardPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EWalletPayment
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $amount
 * @property string $transaction_id
 * @property array|null $payload
 * @property string|null $src_id
 * @property string|null $pay_id
 * @property array|null $source_response
 * @property mixed|null $source_callback_response
 * @property array|null $payment_response
 * @property array|null $re_query_response
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $readable_amount
 * @property-read mixed $readable_created_at
 * @property-read mixed $readable_status
 * @property-read mixed $readable_type
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment wherePaymentResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereReQueryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereSourceCallbackResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereSourceResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereSrcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EWalletPayment whereUserId($value)
 */
	class EWalletPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CardPayment[] $cardPayments
 * @property-read int|null $card_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EWalletPayment[] $eWalletPayments
 * @property-read int|null $e_wallet_payments_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

