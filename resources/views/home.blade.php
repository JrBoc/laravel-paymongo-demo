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
                    EWallet Payments
                    <span class="float-right">
                        <a href="{{ route('e-wallet-payments.create') }}" class="btn btn-primary">Create Payment</a>
                    </span>
                </div>
                <div class="card-body pl-0 pr-0">
                    <table id="dt_payments" class="table table-hover border-bottom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Src ID</th>
                                <th>Pay ID</th>
                                <th style="width: 1%">Status</th>
                                <th>Created At</th>
                                <th style="width: 1%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td class="text-right">{{ $payment->id }}</td>
                                <td>{{ $payment->readable_type }}</td>
                                <td>{{ $payment->transaction_id }}</td>
                                <td>PHP <span class="float-right">{{ number_format($payment->amount, 2) }}</span></td>
                                <td>
                                    @if(in_array($payment->getStatus()['text'], ['Chargeable', 'Pending']))
                                    <a href="{{ $payment->initial_response['redirect']['checkout_url'] }}">{{ $payment->src_id }}</a>
                                    @else
                                    {{ mb_strimwidth($payment->src_id, 0, 15, '...') }}
                                    @endif
                                </td>
                                <td> {{ mb_strimwidth($payment->pay_id, 0, 15, '...') }}</td>
                                <td>
                                    <label style="font-size: 12px" class="p-2 badge badge-{{ $payment->getStatus()['color'] }}">{{ $payment->getStatus()['text'] }}</label>
                                </td>
                                <td>
                                    {{ $payment->readable_created_at }}
                                </td>
                                <td>
                                    @if(in_array($payment->getStatus()['text'], ['Chargeable', 'Pending']))
                                    <form action="{{ route('e-wallet-payments.update', ['payment' => $payment]) }}" method="POST">
                                        @csrf
                                        @method('put')
                                        <button class="btn btn-outline-primary">Re-Query</button>
                                    </form>
                                    @endif
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
