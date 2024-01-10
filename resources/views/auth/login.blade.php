@extends('frontend.layouts.app_plain')
@section('title', "Login")

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class=" text-center">Login</h3>
                    <p class=" text-center">Fill the form to login</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Email">
                            <label for="email">Email</label>
                            @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"  placeholder="Password">
                            <label for="password">Password</label>
                            @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block w-100">
                            {{ __('Login') }}
                        </button>

                        <div class="mb-0 d-flex justify-content-between">

                                <div class="">
                                    <a class="btn btn-link" href="{{ route('register') }}">
                                        Sign Up
                                    </a>
                                </div>

                                <div class="">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    @endif
                                </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
