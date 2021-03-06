@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
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
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-between align-items-center">
                    Card Payments
                    <span class="float-right">
                        <a href="{{ route('card-payment.create') }}" class="btn btn-primary">Create Payment</a>
                    </span>
                </div>
                <div class="card-body pl-0 pr-0">
                    <table id="dt_payments" class="table table-hover border-bottom table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th style="width: 1%">Status</th>
                                <th>Created At</th>
                                <th style="width: 1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td class="text-right">
                                    {{ $payment->id }}
                                </td>
                                <td>
                                    {{ $payment->transaction_id }}
                                </td>
                                <td class="text-right">
                                    {{ $payment->readable_amount }}
                                </td>
                                <td>
                                    <label style="font-size: 12px" class="p-2 badge badge-{{ $payment->readable_status['color'] }}">{{ $payment->readable_status['text'] }}</label>
                                </td>
                                <td>
                                    {{ $payment->readable_created_at }}
                                </td>
                                <td class="">
                                    <div class="d-flex justify-content-between align-items-center text-center">
                                        @if($payment->isAuthenticatable())
                                        <a href="{{ route('card-payment.security_check', $payment) }}" class="btn btn-outline-primary" data-toggle="tooltip" title="Authenticate Card">
                                            <i class="fa fa-credit-card"></i>
                                        </a>
                                        @endif
                                        @if($payment->isPayable())
                                        <a href="{{ route('card-payment.retry', $payment) }}" class="btn btn-outline-primary" data-toggle="tooltip" title="ReTry">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No Payments Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="pl-3 pr-3">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $(document).find('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            boundary: 'window'
        });
    });
</script>
@endpush
