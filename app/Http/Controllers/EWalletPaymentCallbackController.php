<?php

namespace App\Http\Controllers;

use App\Models\EWalletPayment;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;

class EWalletPaymentCallbackController extends Controller
{
    public function success(Request $request)
    {
        $eWalletPayment = EWalletPayment::with('user')->findOrFail($request->id);

        $source = Paymongo::source()->find($eWalletPayment->src_id)->getAttributes();

        if ($eWalletPayment->status == 'pending') {
            $eWalletPayment->update([
                'source_response' => $source,
                'status' => $source['status']
            ]);
        }

        switch ($source['status']) {
            case 'chargeable':
                // Create Payment from Source
                $payment = Paymongo::payment()->create([
                    'amount' => $source['amount'] / 100,
                    'description' => (($eWalletPayment->type == 'gcash') ? 'GCash' : 'GrabPay') . ' Payment for Transaction ID: ' . $eWalletPayment->transaction_id,
                    'currency' => 'PHP',
                    'statement_descriptor' => $eWalletPayment->user->name,
                    'source' => [
                        'id' => $source['id'],
                        'type' => 'source',
                    ]
                ]);

                $payment = $payment->getAttributes();

                $eWalletPayment->update([
                    'payment_response' => $payment,
                    'status' => $payment['status'],
                    'pay_id' => $payment['id'],
                ]);
                break;
            case 'paid':
                if(is_null($eWalletPayment->pay_id)) {
                    $payments = Paymongo::payment()->all();

                    foreach($payments as $payment) {
                        $payment = $payment->getAttributes();

                        if($payment['source']['id']) {{
                            $eWalletPayment->update([
                                'payment_response' => $payment,
                                'pay_id' => $payment['id'],
                                'status' => $payment['status']
                            ]);

                            break;
                        }}
                    }
                }

                $payments = Paymongo::payment()->find($eWalletPayment->pay_id)->getAttributes();

                $eWalletPayment->update([
                    'payment_response' => $payment,
                    'pay_id' => $payment['id'],
                    'status' => $payment['status']
                ]);

                break;
        }

        session()->flash('success', 'Payment for transaction #' . $eWalletPayment->id . ' was updated to <b>' . $eWalletPayment->fresh()->getStatus()['text'] . '</b>');

        return redirect()->route('home');
    }

    public function failed(Request $request)
    {
        $pay = EWalletPayment::with('user')->findOrFail($request->id);

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
