@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">User Management</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="active-users-tab" data-bs-toggle="tab" data-bs-target="#active-users" type="button" role="tab">Active Users</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="deleted-users-tab" data-bs-toggle="tab" data-bs-target="#deleted-users" type="button" role="tab">Deleted Users</button>
                </li>
            </ul>

            <div class="tab-content" id="userTabsContent">
                <div class="tab-pane fade show active" id="active-users" role="tabpanel">
                    <table id="activeUsersTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Roles</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane fade" id="deleted-users" role="tabpanel">
                    <table id="deletedUsersTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Roles</th>
                                <th>Deleted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables for active users
    const activeUsersTable = $('#activeUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.list') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'company_name', name: 'company_name'},
            {data: 'roles', name: 'roles', orderable: false},
            {
                data: 'is_active',
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            {
                data: 'last_login_at',
                name: 'last_login_at',
                render: function(data) {
                    return data ? moment(data).format('YYYY-MM-DD HH:mm:ss') : 'Never';
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = `<a href="/admin/users/${row.id}/edit" class="btn btn-sm btn-primary me-2"><i class="fas fa-edit"></i></a>`;
                    if (!row.roles.includes('admin') && !row.roles.includes('customer')) {
                        actions += `<button class="btn btn-sm btn-danger delete-user" data-id="${row.id}"><i class="fas fa-trash"></i></button>`;
                    }
                    return actions;
                }
            }
        ]
    });

    // Initialize DataTables for deleted users
    const deletedUsersTable = $('#deletedUsersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.list.deleted') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'company_name', name: 'company_name'},
            {data: 'roles', name: 'roles', orderable: false},
            {
                data: 'deleted_at',
                name: 'deleted_at',
                render: function(data) {
                    return moment(data).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-success restore-user" data-id="${row.id}"><i class="fas fa-undo"></i> Restore</button>`;
                }
            }
        ]
    });

    // Delete user handler
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: `/admin/users/${userId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('User deleted successfully');
                    activeUsersTable.ajax.reload();
                    deletedUsersTable.ajax.reload();
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Error deleting user');
                }
            });
        }
    });

    // Restore user handler
    $(document).on('click', '.restore-user', function() {
        const userId = $(this).data('id');
        $.ajax({
            url: `/admin/users/${userId}/restore`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success('User restored successfully');
                activeUsersTable.ajax.reload();
                deletedUsersTable.ajax.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Error restoring user');
            }
        });
    });
});
</script>
@endpush
@endsection 