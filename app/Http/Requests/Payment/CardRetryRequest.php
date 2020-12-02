<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

class CardRetryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'card_number' => [
                'required',
                'string',
                new CardNumber,
            ],
            'expiry_year' => [
                'required',
                new CardExpirationYear($this->get('expiry_month')),
            ],
            'expiry_month' => [
                'required',
                new CardExpirationMonth($this->get('expiry_year')),
            ],
            'cvc' => [
                'required',
                'string',
                'min:3',
                'max:4',
                // new CardCvc($this->get('card_number')),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        [$month, $year] = explode(' / ', $this->get('expiry'));

        $this->merge([
            'card_number' => str_replace(' ', '', $this->get('card_number')),
            'expiry_month' => $month,
            'expiry_year' => $year,
        ]);
    }
}
