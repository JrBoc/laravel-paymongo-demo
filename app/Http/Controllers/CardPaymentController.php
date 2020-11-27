<?php

namespace App\Http\Controllers;

use App\Models\CardPayment;
use Illuminate\Http\Request;
use Paymongo;

class CardPaymentController extends Controller
{
    public function index()
    {
        return view('card_payments.index', [
            'payments' => current_user()->cardPayments()->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('card_payments.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|min:1',
        ]);

        $cardPayment = CardPayment::create([
            'amount' => $request->amount,
            'user_id' => current_user()->id,
        ]);

        $payload = [
            'amount' => $request->amount,
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

        $expiry_date = explode(' / ', $request->expiry);

        $paymentMethod = Paymongo::paymentMethod()->create([
            'type' => 'card',
            'details' => [
                'card_number' => str_replace(' ',  '', $request->card_number),
                'exp_month' => (int) $expiry_date[0],
                'exp_year' => (int) $expiry_date[1],
                'cvc' => $request->cvc,
            ],
            'billing' => [
                'name' => current_user()->name,
                'email' => current_user()->email,
            ]
        ]);

        $cardPayment->update([
            'pm_id' => $paymentMethod->id,
        ]);

        $paymentAttachment = $paymentIntent->attach($paymentMethod->id);

        $cardPayment->update([
            'payment_attach_response' => $paymentAttachment->getAttributes(),
            'status' => $paymentAttachment->status,
        ]);

        switch ($paymentAttachment->status) {
            case 'awaiting_next_action':
                return redirect()->route('card-payments.show', $cardPayment->id);
            case 'succeeded':
                return dd([
                    'SUCCEEDED',
                    $paymentAttachment,
                ]);
            case 'awaiting_payment_method':
                return dd([
                    'CHECK LAST PAYMENT ERROR',
                    $paymentAttachment,
                ]);
            case 'processing':
                return dd([
                    'make server sleep for 2 seconds',
                    $paymentAttachment,
                ]);
        }
    }

    public function show(CardPayment $cardPayment)
    {
        if($cardPayment->status != 'awaiting_next_action') {
            dd($cardPayment);
        }

        return view('card_payments.show', [
            'checkout_url' => $cardPayment->payment_attach_response['next_action']['redirect']['url'],
            'cardPayment' => $cardPayment,
        ]);
    }

    public function update(Request $request, CardPayment $cardPayment)
    {
        $paymentIntent =  Paymongo::paymentIntent()->find($cardPayment->pi_id);

        $cardPayment->update([
            'status' => $paymentIntent->status,
            're_query_response' => $paymentIntent->getAttributes(),
        ]);

        dd($paymentIntent, $cardPayment->toArray());
    }
}
