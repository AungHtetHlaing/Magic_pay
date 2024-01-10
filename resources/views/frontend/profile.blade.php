@extends('frontend.layouts.app')
@section('title', 'Profile')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="d-flex justify-content-center my-4">
                    <img class=" rounded-circle border border-primary"
                        src="https://ui-avatars.com/api/?background=0D6EFD&name={{ $user->name }}" alt="">
                </div>
                <div class="card">
                    <div class="card-body">
                        <ul class=" list-group list-group-flush">
                            <li class=" list-group-item d-flex justify-content-between">
                                <p class=" mb-0">UserName</p>
                                <p class=" mb-0">{{ $user->name }}</p>
                            </li>
                            <li class=" list-group-item d-flex justify-content-between">
                                <p class=" mb-0">Email</p>
                                <p class=" mb-0">{{ $user->email }}</p>
                            </li>
                            <li class=" list-group-item d-flex justify-content-between">
                                <p class=" mb-0">Phone</p>
                                <p class=" mb-0">{{ $user->phone }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <ul class=" list-group list-group-flush">
                            <a href="{{ route('update-password') }}"
                                class=" list-group-item list-group-item-action d-flex justify-content-between">
                                <p class=" mb-0">Update Password</p>
                                <p class=" mb-0"><i class="fa-solid fa-angle-right"></i></p>
                            </a>
                            <li class=" logout list-group-item list-group-item-action d-flex justify-content-between"
                                style="cursor: pointer !important;">
                                <p class=" mb-0">Logout</p>
                                <p class=" mb-0"><i class="fa-solid fa-angle-right"></i></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            $(document).on('click', '.logout', function(event) {
                event.preventDefault();

                Swal.fire({
                    title: "Are you sure to logout?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ajax call to delete
                        $.ajax({
                            method: "POST",
                            url: "{{ route('logout') }}",
                            success: function(res) {
                                window.location.replace("{{ route('login') }}")
                            }
                        });
                    }
                });

            });

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            @if (session('status'))

                Toast.fire({
                    icon: "success",
                    title: "{{ session('status') }}"
                });
            @endif
        })
    </script>
@endpush
