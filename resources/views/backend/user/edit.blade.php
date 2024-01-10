@extends('backend.layouts.app')
@section('title', 'Update User')
@section('user_active', 'mm-active')
@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Update User</div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.user.update', $user->id)}}" method="POST" id="updateUser">
                    @csrf
                    @method('PUT')
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control"  id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Name">
                        <label for="name">Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control"  id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone">
                        <label for="phone">Phone</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control"  id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email">
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        <label for="password">Password</label>

                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-outline-dark back-btn me-2">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

{{-- {!! JsValidator::formRequest('App\Http\Requests\UpdateUserRequest', "#updateUser") !!} --}}


