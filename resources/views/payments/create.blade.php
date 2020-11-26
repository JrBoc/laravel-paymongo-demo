@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Create Payment Request to Paymongo
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.store') }}" id="frm_payment" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="payment_type">Payment Type: <span class="text-danger">*</span></label>
                            <fieldset>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="payment_type" value="gcash" checked> GCash
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="payment_type" value="grab_pay"> Grab Pay
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="payment_type" value="card"> Card
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" name="amount">
                            @error('amount')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button class="btn btn-primary">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
