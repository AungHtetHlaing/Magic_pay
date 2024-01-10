@extends('frontend.layouts.app')
@section('title', 'Transaction Detail')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <div class=" text-center">
                            <img src="{{ asset('images/check.png') }}" alt="" width="60px">
                            <p
                                class="mt-2 @if ($transaction->type == 2) text-danger
                                @elseif ($transaction->type == 1)
                                text-success @endif">
                                {{ number_format($transaction->amount) }} MMK</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class=" text-muted mb-0">Trx ID</p>
                            <p class="mb-0">{{ $transaction->trx_id }}</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class=" text-muted mb-0">Reference Number</p>
                            <p class="mb-0">{{ $transaction->ref_no }}</p>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-between">
                            <p class=" text-muted mb-0">Type</p>
                            @if ($transaction->type == 2)
                                <span class=" badge bg-danger">Expense</span>
                            @elseif ($transaction->type == 1)
                                <span class=" badge bg-success">Income</span>
                            @endif
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class=" text-muted mb-0">Amount</p>
                            <p class="mb-0">{{ $transaction->amount }} MMK</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class=" text-muted mb-0">Date And Time</p>
                            <p class="mb-0">{{ $transaction->created_at }}</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <p class="mb-0">
                                @if ($transaction->type == 2)
                                    To
                                @elseif ($transaction->type == 1)
                                    From
                                @endif
                            </p>
                            <p class=" mb-0">
                                {{ $transaction->source ? $transaction->source->name : '-' }}
                            </p>
                        </div>
                        <hr>

                        @if ($transaction->description)
                            <div class="d-flex justify-content-between">
                                <p class=" text-muted mb-0">Description</p>
                                <p class="mb-0">{{ $transaction->description }}</p>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
