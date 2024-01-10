@extends('backend.layouts.app')
@section('title', 'Reduce Amount')
@section('wallet_active', 'mm-active')
@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-plus icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Reduce Amount</div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="card">
            <div class="card-body">
                @error('fail')
                    <x-alert status='{{"fail"}}' message="{{ $message }}"></x-alert>
                @enderror
                <form action="{{route('admin.wallet.reduce_amount.store')}}" method="POST">
                    @csrf
                    <div class="form-floating mb-3">
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">--Please Choose--</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}} {{$user->phone}}</option>
                            @endforeach
                        </select>
                        <label for="user_id">Users</label>
                        @error('user_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                            value="{{ old('amount') }}" id="amount" name="amount" placeholder="Amount (MMK)">
                        <label for="amount">Amount (MMK)</label>
                        @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            placeholder="Description">{{ old('description') }}</textarea>
                        <label for="description">Description</label>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-outline-dark back-btn me-2">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
