@extends('frontend.layouts.app')
@section('title', 'Receive QR')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body ">
                        <p class=" text-center mb-2">Scan to pay me</p>
                        <div class=" text-center mb-2">
                            {!! QrCode::size(180)->generate($user->phone); !!}
                        </div>
                        <p class=" text-center mb-0">{{$user->name}}</p>
                        <p class=" text-center mb-0">{{$user->phone}}</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


