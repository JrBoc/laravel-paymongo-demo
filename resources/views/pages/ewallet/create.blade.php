@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {!! session('error') !!}
            </div>
            @endif
            @if(session('success'))
            <div class="alert alert-success" role="alert">
                {!! session('success') !!}
            </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    EWallet Payment
                </div>
                <div class="card-body">
                    <form action="{{ route('e-wallet-payment.store') }}" method="POST">
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
                            </fieldset>
                            @include('inc.validation', ['name' => 'payment_type'])
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('amount') is-invalid @enderror" name="amount">
                            @include('inc.validation', ['name' => 'amount'])
                        </div>
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
