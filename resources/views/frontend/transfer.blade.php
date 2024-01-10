@extends('frontend.layouts.app')
@section('title', 'Transfer')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <div class="">
                            <h5>From</h5>
                            <p class=" mb-0">{{$user->name}}</p>
                            <p>{{$user->phone}}</p>
                        </div>
                        @error('fail')
                            <x-alert status='{{"fail"}}' message="{{ $message }}"></x-alert>
                        @enderror
                        <form action="{{route('confirm-transfer')}}" method="get" id="transferForm">

                            <input type="hidden" name="hash_value" id="hash_value">

                            <span class="check-text text-info"></span>
                            <div class="form-floating mb-3 input-group">
                                <input type="number" class="form-control @error('to_phone') is-invalid @enderror"
                                    value="{{ old('to_phone') }}" id="to_phone" name="to_phone"
                                    placeholder="To">
                                <div class="input-group-text bg-primary text-white check-btn"><i class=" fa-solid fa-check"></i></div>
                                <label for="to_phone">To</label>
                                @error('to_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}" id="amount" name="amount"
                                    placeholder="Amount (MMK)">
                                <label for="amount">Amount (MMK)</label>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <textarea  class="form-control @error('description') is-invalid @enderror"
                                     id="description" name="description"
                                    placeholder="Description">{{ old('description') }}</textarea>
                                <label for="description">Description</label>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block w-100 submit-btn">
                                Continuous
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $(".check-btn").on("click", function() {
                let phone = $('#to_phone').val();
                $.ajax({
                    method: "GET",
                    url: `/check-phone?to_phone=${phone}`,
                    success: function(res) {
                        if(res.status == "success") {
                            $(".check-text").text(`(${res.data.name})`);
                        } else {
                            $(".check-text").text(`(${res.message})`);
                        }
                    }
                })
            });

            $(".submit-btn").on('click', function(event) {
                event.preventDefault();

                let to_phone = $("#to_phone").val();
                let amount = $("#amount").val();
                let description = $("#description").val();

                $.ajax({
                    url: `/transfer-hash?to_phone=${to_phone}&amount=${amount}&description=${description}`,
                    method: "GET",
                    success: function(res) {
                        if(res.status == "success") {
                            $("#hash_value").val(res.hash_value);
                            $("#transferForm").submit();
                        }
                    }
                });
            });
        })
    </script>
@endpush


