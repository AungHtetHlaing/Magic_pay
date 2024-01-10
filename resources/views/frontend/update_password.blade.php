@extends('frontend.layouts.app')
@section('title', 'Update Password')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body bg-white">

                        <div class="d-flex justify-content-center my-3">
                            <img class=" w-25" src="{{ asset('images/update_password.png') }}" alt="">
                        </div>
                        @error('fail')
                            <x-alert message="{{ $message }}"></x-alert>
                        @enderror
                        <form action="{{route('update-password.store')}}" method="POST">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('old_password') is-invalid @enderror"
                                    value="{{ old('old_password') }}" id="old_password" name="old_password"
                                    placeholder="Old Password">
                                <label for="old_password">Old Password</label>
                                @error('old_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                    value="{{ old('new_password') }}" id="new_password" name="new_password"
                                    placeholder="New Password">
                                <label for="new_password">New Password</label>
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-block w-100">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
