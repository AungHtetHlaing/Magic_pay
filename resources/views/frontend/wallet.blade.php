@extends('frontend.layouts.app')
@section('title', 'Wallet')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body bg-primary-subtle">
                        <div class="">
                            <h4 class=" text-uppercase">Account Number</h4>
                            <p class=" fs-5">{{$user->wallet ? $user->wallet->account_number : "-"}}</p>
                        </div>
                        <div class="">
                            <h4 class=" text-uppercase">Balance</h4>
                            <p class=" fs-5">{{$user->wallet ? $user->wallet->amount : 0}} MMK</p>
                        </div>
                        <div class="">
                            <h5 class=" text-uppercase">{{$user->name}}</h5>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


