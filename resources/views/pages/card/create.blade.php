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
                    Create Card Payment
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>NOTE:</strong> Do not send the credit card information to your backend, sending the information to your backend requires your platform to conform with PCI-DSS regulations.
                        <hr>
                        <small> For demo purposes this form sends the Credit Card information to the backend.</small>
                    </div>
                    <form id="frm_payment" action="{{ route('card-payment.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amount">
                        </div>
                        <div class="card-display mb-3">
                        </div>
                        <div class="form-group">
                            <input class="form-control @error('card_number') is-invalid @enderror" type="text" name="card_number" placeholder="Card Number">
                            @include('inc.validation', ['name' => 'card_number'])
                        </div>
                        <div class="form-group">
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Full Name">
                            @include('inc.validation', ['name' => 'name'])
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input class="form-control @error('expiry') is-invalid @enderror" type="text" name="expiry" placeholder="MM/YYYY">
                                <input class="form-control @error('cvc') is-invalid @enderror" type="text" name="cvc" placeholder="CVC">
                            </div>
                            @include('inc.validation', ['name' => 'expiry'])
                            @include('inc.validation', ['name' => 'cvc'])
                        </div>
                        <button class="btn btn-primary" type="submit">SUBMIT</button>
                    </form>
                    @php
                        dump($errors, request()->all());
                    @endphp
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var card = new Card({
            form: '#frm_payment',
            container: '.card-display',
            formSelectors: {
                numberInput: 'input[name="card_number"]',
            },
        });
    });
</script>
@endpush
