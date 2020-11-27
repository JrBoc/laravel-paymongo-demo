@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Create Card Payment Request
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>NOTE:</strong> Do not send the credit card information to your backend, sending the information to your backend requires your platform to conform with PCI-DSS regulations.
                        <hr>
                        <small> For demo purposes this form sends the Credit Card information to the backend.</small>
                    </div>
                    <form id="frm_payment" action="{{ route('card-payments.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="amount">
                        </div>
                        <div class="card-display mb-3">

                        </div>
                        <div class="form-group">
                            <input class="form-control @error('card_number') is-invalid @enderror" type="text" name="card_number" placeholder="Card Number">
                        </div>
                        <div class="form-group">
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Full Name">
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <input class="form-control @error('expiry') is-invalid @enderror" type="text" name="expiry" placeholder="MM/YY">
                                <input class="form-control @error('cvc') is-invalid @enderror" type="text" name="cvc" placeholder="CVC">
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var card = new Card({
            form: '#frm_payment',
            container: '.card-display',
            formSelectors: {
                numberInput: 'input[name="card_number"]', // optional
            },
        });
    });

</script>
@endpush
