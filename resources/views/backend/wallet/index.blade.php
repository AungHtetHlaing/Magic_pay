@extends('backend.layouts.app')
@section('title', 'Wallets')
@section('wallet_active', 'mm-active')
@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-wallet icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Wallets</div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="mb-2">
            <a href="{{route('admin.wallet.add_amount')}}" class="btn btn-primary "><i class="pe-7s-plus"></i> Add Amount</a>
            <a href="{{route('admin.wallet.reduce_amount')}}" class="btn btn-danger"><i class="pe-7s-junk"></i> Reduce Amount</a>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="wallets" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Account Number</th>
                            <th>Account Person</th>
                            <th>Amount (MMK)</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(document).ready(function() {

            let datatable = new DataTable('#wallets', {
                ajax: '/admin/wallet/datatable/ssd',
                processing: true,
                serverSide: true,
                columns: [
                    {data: "account_number", name: "account_number"},
                    {data: "account_person", name: "account_person"},
                    {data: "amount", name: "amount"},
                    {data: "created_at", name: "created_at"},
                    {data: "updated_at", name: "updated_at"},
                ],
                order: [[4, 'desc']],
                columnDefs: [
                    { orderable: true, className: 'reorder', targets: 4 },
                    { orderable: false, targets: '_all' }
                ]
            });
        });
    </script>
@endpush
