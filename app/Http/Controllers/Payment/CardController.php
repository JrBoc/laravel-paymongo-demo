<?php

namespace App\Http\Controllers\Payment;

use App\Models\CardPayment;
use App\Http\Controllers\Controller;
use Luigel\Paymongo\Facades\Paymongo;
use App\Http\Requests\Payment\CardStoreRequest;
use LVR\CreditCard\Cards\Card;

class CardController extends Controller
{
    public function index()
    {
        return view('pages.card.index', [
            'payments' => current_user()->cardPayments()->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('pages.card.create');
    }

    public function store(CardStoreRequest $request)
    {
        $data = $request->validated();

        $cardPayment = CardPayment::create([
            'amount' => $data['amount'],
            'user_id' => current_user()->id,
        ]);

        $payload = [
            'amount' => $data['amount'],
            'payment_method_allowed' => [
                'card'
            ],
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'automatic'
                ]
            ],
            'description' => 'Card Payment for Transaction ID' . $cardPayment->transaction_id,
            'statement_descriptor' => 'TESTING STORE',
            'currency' => 'PHP',
        ];

        $cardPayment->update([
            'payload' => $payload,
        ]);

        $paymentIntent = Paymongo::paymentIntent()->create($payload);

        $cardPayment->update([
            'pi_id' => $paymentIntent->id,
            'payment_intent_response' => $paymentIntent->getAttributes(),
            'status' => $paymentIntent->status,
        ]);

        $paymentMethod = Paymongo::paymentMethod()->create([
            'type' => 'card',
            'details' => [
                'card_number' => $data['card_number'],
                'exp_month' => (int) $data['expiry_month'],
                'exp_year' => (int) $data['expiry_year'],
                'cvc' => $data['cvc'],
            ],
            'billing' => [
                'name' => current_user()->name,
                'email' => current_user()->email,
            ]
        ]);

        $cardPayment->update([
            'pm_id' => $paymentMethod->id,
            'payment_method_response' => $paymentMethod->getAttributes(),
        ]);

        try {
            $paymentIntent = $paymentIntent->attach($paymentMethod->id);
        } catch (\Luigel\Paymongo\Exceptions\BadRequestException $e) {
            $exception = json_decode($e->getMessage(),  true);

            return redirect()->back()->with('error', $exception['errors'][0]['detail']);
        }

        $cardPayment->update([
            'payment_attach_response' => $paymentIntent->getAttributes(),
            'status' => $paymentIntent->status,
        ]);

        switch ($paymentIntent->status) {
            case 'awaiting_next_action':
                return redirect()->route('card-payment.security_check', $cardPayment);
            case 'succeeded':
                return dd([
                    'SUCCEEDED',
                    $paymentIntent,
                ]);
            case 'awaiting_payment_method':
                return dd([
                    'CHECK LAST PAYMENT ERROR',
                    $paymentIntent,
                ]);
            case 'processing':
                return dd([
                    'make server sleep for 2 seconds',
                    $paymentIntent,
                ]);
        }
    }

    public function show(CardPayment $cardPayment)
    {
        $paymentIntent =  Paymongo::paymentIntent()->find($cardPayment->pi_id);
    }

    public function update(CardPayment $cardPayment)
    {
        $paymentIntent =  Paymongo::paymentIntent()->find($cardPayment->pi_id);

        if ($paymentIntent->status == 'awaiting_next_action') {
            return redirect()->route('card-payment.security_check', $cardPayment);
        }

        $cardPayment->update([
            'status' => $paymentIntent->status,
            're_query_response' => $paymentIntent->getAttributes(),
        ]);

        return $this->flashAndRedirect('Payment for transaction ID ' . $cardPayment->id . ' ' . (($cardPayment->status == 'succeeded') ? 'was successful' : 'failed'));
    }

    public function securityCheck(CardPayment $cardPayment)
    {
        if ($cardPayment->status != 'awaiting_next_action') {
            return $this->flashAndRedirect('Payment does not require, additional security checks.');
        }

        return view('pages.card.security_check', [
            'cardPayment' => $cardPayment,
        ]);
    }

    public function retry(CardPayment $cardPayment)
    {
        if ($cardPayment->status != 'awaiting_next_action') {
            return $this->flashAndRedirect('Payment does not require, additional security checks.');
        }

        return view('pages.card.security_check', [
            'cardPayment' => $cardPayment,
        ]);
    }

    private function flashAndRedirect($message = '', $type = 'success')
    {
        session()->flash($type, $message);
        return redirect()->route('card-payment.index');
    }
}
