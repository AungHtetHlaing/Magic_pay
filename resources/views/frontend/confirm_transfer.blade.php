@extends('frontend.layouts.app')
@section('title', 'Transfer')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        @error('fail')
                            <x-alert status='{{"fail"}}' message="{{ $message }}"></x-alert>
                        @enderror
                        <form action="{{ route('complete-transfer') }}" method="post" id="confirmForm">
                            @csrf
                            <input type="hidden" name="hash_value" value="{{ $hash_value }}">
                            <input type="hidden" name="to_phone" value="{{ $to_user->phone }}">
                            <input type="hidden" name="amount" value="{{ $amount }}">
                            <input type="hidden" name="description" value="{{ $description }}">

                            <div class="">
                                <h5>From</h5>
                                <p class=" mb-0">{{ $user->name }}</p>
                                <p>{{ $user->phone }}</p>
                            </div>

                            <div class="">
                                <h5>To</h5>
                                <p class=" mb-0">{{ $to_user->name }}</p>
                                <p>{{ $to_user->phone }}</p>
                            </div>
                            <div class="">
                                <h5>Amount</h5>
                                <p>{{ $amount }} MMK</p>
                            </div>

                            @if ($description)
                                <div class="">
                                    <h5>Description</h5>
                                    <p>{{ $description }}</p>
                                </div>
                            @endif



                            <button id="confirm_btn" type="submit" class="btn btn-primary btn-block w-100">
                                Confirm
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

            $('#confirm_btn').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: "Please Fill your current password",
                    html: `<input type="password" class="form-control current_password">`,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "Confirm",
                    cancelButtonText: "Cancel",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        let password = $('.current_password').val();
                        $.ajax({
                            method: "GET",
                            url: `/check-password?password=${password}`,
                            success: function(res) {
                                if (res.status == "success") {
                                    $("#confirmForm").submit();
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: res.message,
                                        icon: 'error',
                                        confirmButtonText: 'Ok'
                                    })
                                }
                            }
                        })
                    }
                });
            });
        })
    </script>
@endpush
