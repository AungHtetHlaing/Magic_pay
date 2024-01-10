@extends('frontend.layouts.app')
@section('title', 'Transaction')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="date">Date</label>
                                    <input type="text" class=" form-control date" value="{{request()->date}}" placeholder="All">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="inputGroupSelect01">Type</label>
                                    <select class="form-select" id="type">
                                        <option value="">All</option>
                                        <option value="1" @if (request()->type == 1) selected @endif>Income
                                        </option>
                                        <option value="2" @if (request()->type == 2) selected @endif>Expense
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="infinite-scroll">
                    @foreach ($transactions as $transaction)
                        <a href="{{ route('transaction-detail', $transaction->trx_id) }}" class="text-decoration-none">
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h6 class="mb-0">Trx ID: {{ $transaction->trx_id }}</h6>
                                        <p
                                            class="mb-0 @if ($transaction->type == 2) text-danger
                                        @elseif ($transaction->type == 1)
                                        text-success @endif">
                                            {{ number_format($transaction->amount) }} MMK</p>
                                    </div>
                                    <p class=" mb-1">
                                        @if ($transaction->type == 2)
                                            To
                                        @elseif ($transaction->type == 1)
                                            From
                                        @endif
                                        {{ $transaction->source ? $transaction->source->name : '-' }}
                                    </p>
                                    <p class="mb-1">
                                        {{ $transaction->created_at }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    {{ $transactions->links() }}
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

            $('.date').daterangepicker({
                "singleDatePicker": true,
                "autoApply": false,
                "autoUpdateInput" : false,
                "locale": {
                    "format": "YYYY-MM-DD",
                },

            });

            $('.date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format("YYYY-MM-DD HH:mm:ss"));

                let date = $(".date").val();
                let type = $("#type").val();
                history.pushState(null, '', `?date=${date}&&type=${type}`);
                window.location.reload();
            });

            $('.date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val("");

                let date = $(".date").val();
                let type = $("#type").val();
                history.pushState(null, '', `?date=${date}&&type=${type}`);
                window.location.reload();
            });

            $("#type").change(function() {
                let date = $(".date").val();
                let type = $("#type").val();
                history.pushState(null, '', `?date=${date}&&type=${type}`);
                window.location.reload();
            })
        })
    </script>
@endpush
