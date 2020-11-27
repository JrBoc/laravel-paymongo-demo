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
                    <iframe src="{{ $checkout_url }}" frameborder="0" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
        <form id="frm_update" action="{{ route('card-payments.update', $cardPayment) }}" method="POST" style="display: none">
            @csrf
            @method('put')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.addEventListener('message', function (e) {
        document.getElementById('frm_update').submit();
    });
</script>
@endpush
