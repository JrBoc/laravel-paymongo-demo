<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Luigel\Paymongo\Facades\Paymongo;

class PaymentController extends Controller
{
    public function create()
    {
        return view('payments.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required|in:gcash,grab_pay',
            'amount' => 'required|numeric|min:100',
        ]);

        $payment = Payment::create([
            'user_id' => auth()->user()->id,
            'status' => 'initial',
            'amount' => $request->amount,
            'type' => $request->payment_type,
        ]);

        $payload = [
            'type' => $request->payment_type,
            'amount' => (int) $request->amount,
            'currency' => 'PHP',
            'redirect' => [
                'success' => route('payments_callback.success', ['id' => $payment->id]),
                'failed' =>  route('payments_callback.failed', ['id' => $payment->id]),
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

    public function update(Payment $payment)
    {
        if (in_array($payment->type, ['gcash', 'grab_pay'])) {
            try {
                $source = Paymongo::source()->find($payment->src_id)->getAttributes();

                if($payment->status == $source['status']) {
                    session()->flash('error', 'No changes was made to transaction #' . $payment->id .'. No changes was found.');

                    return redirect()->route('home');
                }

                $payment->update([
                    'status' => $source['status'],
                    're_query_response' => $source,
                ]);

                session()->flash('success', 'Payment for transaction #' . $payment->id . ' was updated to <b>' . $payment->fresh()->getStatus()['text'] . '</b>');
            } catch (\Luigel\Paymongo\Exceptions\MethodNotFoundException $e) {
                $payment->update([
                    'status' => 'failed',
                ]);

                session()->flash('error', 'Payment for transaction #' . $payment->id . ' was not found in Paymongo');
            }
        }

        return redirect()->route('home');
    }
}
