@extends('frontend.layouts.app')
@section('title', 'Magic Pay')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class=" text-center my-4">
                    <img class=" rounded-circle border border-primary"
                        src="https://ui-avatars.com/api/?background=0D6EFD&name={{ $user->name }}" alt="">
                    <p class=" mb-0">{{ $user->name }}</p>
                    <p class=" mb-0">{{ $user->wallet ? $user->wallet->amount : 0 }} MMK</p>
                </div>
                <div class="row ">
                    <div class="col-md-6">
                        <a href="{{ route('scan-and-pay') }}" class=" text-decoration-none">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('images/qr-code-scan.png') }}" alt=""
                                        style="width: 40px; height:40px">
                                    <span>Scan and Pay</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('receive-qr') }}" class=" text-decoration-none">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('images/qr-code.png') }}" alt=""
                                        style="width: 40px; height:40px">
                                    <span>Receive with QR</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <ul class=" list-group list-group-flush">
                            <a href="{{ route('transfer') }}"
                                class=" list-group-item list-group-item-action d-flex justify-content-between">
                                <p class=" mb-0">Transfer</p>
                                <p class=" mb-0"><i class="fa-solid fa-angle-right"></i></p>
                            </a>
                            <a href="{{ route('wallet') }}"
                                class=" list-group-item list-group-item-action d-flex justify-content-between">
                                <p class=" mb-0">Wallet</p>
                                <p class=" mb-0"><i class="fa-solid fa-angle-right"></i></p>
                            </a>
                            <a href="{{ route('transaction') }}"
                                class=" list-group-item list-group-item-action d-flex justify-content-between">
                                <p class=" mb-0">Transaction</p>
                                <p class=" mb-0"><i class="fa-solid fa-angle-right"></i></p>
                            </a>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
