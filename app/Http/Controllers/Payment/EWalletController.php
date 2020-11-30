<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Models\EWalletPayment;
use App\Http\Controllers\Controller;
use Luigel\Paymongo\Facades\Paymongo;
use App\Http\Requests\Payment\EWalletStoreRequest;

class EWalletController extends Controller
{
    public function index()
    {
        return view('pages.ewallet.index', [
            'payments' => current_user()->eWalletPayments()->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('pages.ewallet.create');
    }

    public function store(EWalletStoreRequest $request)
    {
        $data = $request->validated();

        $eWalletPayment = EWalletPayment::create([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'type' => $request->payment_type,
            'status' => 'initial',
        ]);

        $payload = [
            'type' => $data['payment_type'],
            'amount' => $data['amount'],
            'currency' => 'PHP',
            'redirect' => [
                'success' => route('e-wallet-payment.callback_success', $eWalletPayment),
                'failed' =>  route('e-wallet-payment.callback_failed', $eWalletPayment),
            ]
        ];

        $eWalletPayment->update([
            'payload' => $payload
        ]);

        $source = Paymongo::source()->create($payload);

        $eWalletPayment->update([
            'src_id' => $source->id,
            'source_response' => $source->getAttributes(),
            'status' => $source->status,
        ]);

        return redirect($source->redirect['checkout_url']);
    }

    public function update(EWalletPayment $eWalletPayment)
    {
        try {
            $source = Paymongo::source()->find($eWalletPayment->src_id);
        } catch (\Luigel\Paymongo\Exceptions\NotFoundException $e) {
            $eWalletPayment->update([
                'status' => 'not_found',
            ]);

            return $this->flashAndRedirect('Payment for transaction ID ' . $eWalletPayment->transaction_id . ' was not found. No Record was found in PAYMONGO Services', 'error');
        }

        if ($eWalletPayment->status == $source->status) {
            session()->flash('error', 'No changes was made to transaction #' . $eWalletPayment->transaction_id . '. No changes was found.');
            return redirect()->route('e-wallet-payment.index');
        }

        $eWalletPayment->update([
            'status' => $source->status,
            're_query_response' => $source,
        ]);

        return $this->flashAndRedirect('Payment for transaction ID ' . $eWalletPayment->id . ' ' . (($eWalletPayment->status == 'paid') ? 'was successful' : 'failed'));
    }


    private function flashAndRedirect($message = '', $type = 'success')
    {
        session()->flash($type, $message);
        return redirect()->route('e-wallet-payment.index');
    }
}
