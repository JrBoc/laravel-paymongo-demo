<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;

class PaymentCallbackController extends Controller
{
    public function success(Request $request)
    {
        $pay = Payment::with('user')->findOrFail($request->id);

        $source = Paymongo::source()->find($pay->src_id)->getAttributes();

        if ($pay->status == 'pending') {
            $pay->update([
                'source_response' => $source,
                'status' => $source['status']
            ]);
        }

        switch ($source['status']) {
            case 'chargeable':
                // Create Payment from Source
                $payment = Paymongo::payment()->create([
                    'amount' => $source['amount'] / 100,
                    'description' => 'Gcash Payment for ID: ' . $pay->id,
                    'currency' => 'PHP',
                    'statement_descriptor' => $pay->user->name,
                    'source' => [
                        'id' => $source['id'],
                        'type' => 'source',
                    ]
                ]);

                $payment = $payment->getAttributes();

                $pay->update([
                    'payment_response' => $payment,
                    'status' => $payment['status'],
                    'pay_id' => $payment['id'],
                ]);
                break;
            case 'paid':
                if(is_null($pay->pay_id)) {
                    $payments = Paymongo::payment()->all();

                    foreach($payments as $payment) {
                        $payment = $payment->getAttributes();

                        if($payment['source']['id']) {{
                            $pay->update([
                                'payment_response' => $payment,
                                'pay_id' => $payment['id'],
                                'status' => $payment['status']
                            ]);

                            break;
                        }}
                    }
                }

                $payments = Paymongo::payment()->find($pay->pay_id)->getAttributes();

                $pay->update([
                    'payment_response' => $payment,
                    'pay_id' => $payment['id'],
                    'status' => $payment['status']
                ]);

                break;
        }

        session()->flash('success', 'Payment for transaction #' . $pay->id . ' was updated to <b>' . $pay->fresh()->getStatus()['text'] . '</b>');

        return redirect()->route('home');
    }

    public function failed(Request $request)
    {
        $pay = Payment::with('user')->findOrFail($request->id);

        $source = Paymongo::source()->find($pay->src_id)->getAttributes();

        if ($pay->status == 'pending') {
            $pay->update([
                'source_response' => $source,
                'status' => $source['status'],
            ]);
        }

        session()->flash('success', 'Payment for transaction #' . $pay->id . ' was updated to <b>' . $pay->fresh()->getStatus()['text'] . '</b>');

        return redirect()->route('home');
    }
}
