@extends('backend.layouts.app')
@section('title', 'User Management')
@section('user_active', 'mm-active')
@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>User Management</div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="">
            <a href="{{route('admin.user.create')}}" class="btn btn-primary mb-2"> <i class="pe-7s-plus icon-gradient bg-mean-fruit"></i> Create User</a>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="admin_users" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>IP</th>
                            <th>User Agent</th>
                            <th>Login at</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th>actions</th>
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

            let datatable = new DataTable('#admin_users', {
                ajax: '/admin/user/datatable/ssd',
                processing: true,
                serverSide: true,
                columns: [
                    {data: "name", name: "name"},
                    {data: "email", name: "email"},
                    {data: "phone", name: "phone"},
                    {data: "ip", name: "ip",  searchable: false},
                    {data: "user_agent", name: "user_agent",  searchable: false},
                    {data: "login_at", name: "login_at",  searchable: false},
                    {data: "created_at", name: "created_at"},
                    {data: "updated_at", name: "updated_at"},
                    {data: "actions", name: "actions",sortable:false, searchable: false},
                ],
                order: [[6, 'desc']],
                columnDefs: [
                    { orderable: true, className: 'reorder', targets: 0 },
                    { orderable: false, targets: '_all' }
                ]
            });


            $(document).on('click', '.delete_btn',function(event){
                event.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                if (result.isConfirmed) {
                    // Ajax call to delete
                    $.ajax({
                    method: "DELETE",
                    url: `/admin/user/${id}`,
                    success: function(res) {
                        datatable.ajax.reload();
                    }
                });
                }
                });


            })
        });
    </script>
@endpush
