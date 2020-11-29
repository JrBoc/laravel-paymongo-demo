<?php

namespace App\Http\Controllers\Payment;

use App\Models\EWalletPayment;
use App\Http\Controllers\Controller;
use Luigel\Paymongo\Facades\Paymongo;

class EWalletCallbackController extends Controller
{
    public function success(EWalletPayment $eWalletPayment)
    {
        $source = Paymongo::source()->find($eWalletPayment->src_id);

        if ($eWalletPayment->status == 'pending') {
            $eWalletPayment->update([
                'source_callback_response' => $source->getAttributes(),
                'status' => $source->status,
            ]);
        }

        switch ($source->status) {
            case 'chargeable':
                $payment = Paymongo::payment()->create([
                    'amount' => $eWalletPayment->amount,
                    'description' => $eWalletPayment->readable_type . ' Payment for Transaction ID: ' . $eWalletPayment->transaction_id,
                    'currency' => 'PHP',
                    'statement_descriptor' => $eWalletPayment->user->name,
                    'source' => [
                        'id' => $source->id,
                        'type' => 'source',
                    ]
                ]);

                $eWalletPayment->update([
                    'payment_response' => $payment->getAttributes(),
                    'pay_id' => $payment->id,
                    'status' => $payment->status
                ]);
                break;
            case 'paid':
                if (!$eWalletPayment->pay_id) {
                    foreach (Paymongo::payment()->all() as $payment) {
                        if ($payment->source['id'] == $eWalletPayment->src_id) {
                            $eWalletPayment->update([
                                'payment_response' => $payment->getAttributes(),
                                'pay_id' => $payment->id,
                                'status' => $payment->status
                            ]);
                            break;
                        }
                    }
                } else {
                    $payment = Paymongo::payment()->find($eWalletPayment->pay_id);

                    $eWalletPayment->update([
                        'payment_response' => $payment->getAttributes(),
                        'pay_id' => $payment->id,
                        'status' => $payment->status
                    ]);
                }
                break;
        }

        session()->flash('success', 'Payment for transaction ID ' . $eWalletPayment->id . ' was successful');

        return redirect()->route('ewallet.index');
    }

    public function failed(EWalletPayment $eWalletPayment)
    {
        $source = Paymongo::source()->find($eWalletPayment->src_id);

        if($eWalletPayment->status == 'pending') {
            $eWalletPayment->update([
                'source_callback_response' => $source->getAttributes(),
                'status' => 'fail',
            ]);
        }

        session()->flash('success', 'Payment for transaction ID ' . $eWalletPayment->id . ' failed');

        return redirect()->route('ewallet.index');
    }
}
