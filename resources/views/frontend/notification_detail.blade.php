@extends('frontend.layouts.app')
@section('title', 'Notification Detail')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body ">
                        <div class="text-center">
                            <img class="w-25" src="{{asset('images/notification.png')}}" alt="">
                            <h6 class=" mb-1">{{$notification->data['title']}}</h6>
                            <p class=" mb-1">{{$notification->data['message']}}</p>
                            <p class=" mb-1">{{$notification->created_at}}</p>
                            <a href="{{$notification->data['web_link']}}" class="btn btn-primary">Continuous</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


