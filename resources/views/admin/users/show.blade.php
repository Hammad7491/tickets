@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i> Users List</h4>
      <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm d-flex align-items-center">
        <i class="bi bi-person-plus-fill me-1"></i> Add User
      </a>
    </div>

    <div class="card-body p-4">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
          <i class="bi bi-check-circle-fill me-2 fs-4"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Blocked Users Count --}}
      @php
        $blockedCount = $users->where('is_blocked', true)->count();
      @endphp
      <div class="mb-3">
        <span class="fw-semibold">Blocked Users:</span>
        <span class="badge bg-danger">{{ $blockedCount }}</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th><i class="bi bi-person-fill me-1"></i> Name</th>
              <th><i class="bi bi-envelope-fill me-1"></i> Email</th>
              <th><i class="bi bi-telephone-fill me-1"></i> Phone</th>
              <th><i class="bi bi-shield-lock-fill me-1"></i> Roles</th>
              <th><i class="bi bi-activity me-1"></i> Status</th>
              <th class="text-end"><i class="bi bi-gear-fill me-1"></i> Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              <tr>
                <td class="fw-semibold">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? '—' }}</td>
                <td>
                  @forelse($user->roles as $role)
                    <span class="badge bg-secondary me-1">{{ ucfirst($role->name) }}</span>
                  @empty
                    <span class="text-muted">No role</span>
                  @endforelse
                </td>
                <td>
                  @if($user->is_blocked)
                    <span class="badge text-bg-danger">Blocked</span>
                  @else
                    <span class="badge text-bg-success">Active</span>
                  @endif
                </td>
                <td class="text-end">
                  {{-- Edit Button --}}
                  <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                  </a>

                  {{-- Block / Unblock Button --}}
                  @if(!$user->is_blocked)
                    <form action="{{ route('admin.users.block', $user) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-warning me-1"
                        onclick="return confirm('Block this user? They will not be able to log in.')"
                        title="Block User">
                        <i class="bi bi-person-slash"></i>
                      </button>
                    </form>
                  @else
                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-success me-1"
                        onclick="return confirm('Unblock this user?')"
                        title="Unblock User">
                        <i class="bi bi-person-check"></i>
                      </button>
                    </form>
                  @endif

                  {{-- Delete Button --}}
                  <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                      onclick="return confirm('Delete this user?')" title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
