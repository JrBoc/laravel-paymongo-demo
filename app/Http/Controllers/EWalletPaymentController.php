<?php

namespace App\Http\Controllers;

use App\Models\EWalletPayment;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;

class EWalletPaymentController extends Controller
{
    public function index()
    {
        return view('ewallet_payments.index', [
            'payments' => current_user()->eWalletPayments()->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('ewallet_payments.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required|in:gcash,grab_pay',
            'amount' => 'required|numeric|min:100',
        ]);

        $payment = EWalletPayment::create([
            'user_id' => auth()->user()->id,
            'status' => 'initial',
            'amount' => $request->amount,
            'type' => $request->payment_type,
        ]);

        $payload = [
            'type' => $request->payment_type,
            'amount' => $request->amount,
            'currency' => 'PHP',
            'redirect' => [
                'success' => route('ewallet_payments_callback.success', ['id' => $payment->id]),
                'failed' =>  route('ewallet_payments_callback.failed', ['id' => $payment->id]),
            ]
        ];

        $payment->update([
            'payload' => $payload
        ]);

        $source = Paymongo::source()->create($payload);

        $payment->update([
            'src_id' => $source->id,
            'initial_response' => $source->getAttributes(),
            'status' => 'pending',
        ]);

        return redirect()->to($source->redirect['checkout_url']);
    }

    public function update(EWalletPayment $eWalletPayment)
    {
        if (in_array($eWalletPayment->type, ['gcash', 'grab_pay'])) {
            try {
                $source = Paymongo::source()->find($eWalletPayment->src_id)->getAttributes();

                if($eWalletPayment->status == $source['status']) {
                    session()->flash('error', 'No changes was made to transaction #' . $eWalletPayment->transaction_id .'. No changes was found.');

                    return redirect()->route('home');
                }

                $eWalletPayment->update([
                    'status' => $source['status'],
                    're_query_response' => $source,
                ]);

                session()->flash('success', 'Payment for transaction #' . $eWalletPayment->transaction_id . ' was updated to <b>' . $eWalletPayment->fresh()->getStatus()['text'] . '</b>');
            } catch (\Luigel\Paymongo\Exceptions\MethodNotFoundException $e) {
                $eWalletPayment->update([
                    'status' => 'failed',
                ]);

                session()->flash('error', 'Payment for transaction #' . $eWalletPayment->transaction_id . ' was not found in Paymongo');
            }
        }

        return redirect()->route('ewallet-payments.index');
    }
}
