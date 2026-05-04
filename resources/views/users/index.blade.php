@extends('layouts.app')

@section('title', 'Users List')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Users List</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-custom">
                <i class="bi bi-plus-circle"></i> Add New User
            </a>
        </div>
    </div>

    @if ($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Name</th>
                        <th style="width: 25%">Email</th>
                        <th style="width: 15%">Phone</th>
                        <th style="width: 25%">Address</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><strong>{{ $user->id }}</strong></td>
                            <td>{{ $user->name }}</td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ Str::limit($user->address, 30) }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning btn-custom" title="Edit">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-custom" onclick="return confirmDelete('{{ $user->name }}');" title="Delete">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="no-data">
            <h3 class="text-muted mb-3">📭 No Users Found</h3>
            <p class="text-muted mb-3">There are no users in the system yet.</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-custom">
                Create First User
            </a>
        </div>
    @endif
@endsection
