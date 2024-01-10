@extends('frontend.layouts.app')
@section('title', 'Notifications')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="infinite-scroll">
                    @foreach ($notifications as $notification)
                    <div class="">
                        <a class="text-danger delete-btn d-block text-end" href="#" data-id="{{$notification->id}}">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                        <a href="{{ route('notification-detail', $notification->id) }}" class="text-decoration-none">
                            <div class="card mb-2  @if (!is_null($notification->read_at))
                                text-muted
                            @endif">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">{{ Str::limit($notification->data['title'], 30, '...') }}</h6>
                                        <p class="mb-0" >{{ $notification->created_at->diffForHumans()}}</p>
                                    </div>


                                    <div class="d-flex justify-content-between">
                                        <p class="mb-1">
                                            {{ Str::limit($notification->data['message'], 50, '...') }}
                                        </p>

                                    </div>
                                </div>
                            </div>
                        </a>

                    </div>
                    @endforeach

                    {{ $notifications->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('ul.pagination').hide();
            $(function() {
                $('.infinite-scroll').jscroll({
                    autoTrigger: true,
                    loadingHtml: '<p>Loading...</p>',
                    padding: 0,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.infinite-scroll',
                    callback: function() {
                        $('ul.pagination').remove();
                    }
                });
            });

            $(".delete-btn").on("click", function(event) {
                event.preventDefault();
                let id = $(this).data('id');

                $.ajax({
                    method: "DELETE",
                    url: `/notification/${id}`,
                    success: function(res) {
                        window.location.reload();
                    }
                })
            });
        })
    </script>
@endpush
