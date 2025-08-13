{{-- resources/views/admin/users/show.blade.php (responsive) --}}
@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h4 class="mb-0 d-flex align-items-center">
        <i class="bi bi-people-fill me-2"></i> Users List
      </h4>
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
      @php $blockedCount = $users->where('is_blocked', true)->count(); @endphp
      <div class="mb-3">
        <span class="fw-semibold">Blocked Users:</span>
        <span class="badge bg-danger">{{ $blockedCount }}</span>
      </div>

      {{-- TABLE (md and up) --}}
      <div class="table-responsive d-none d-md-block">
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
                <td class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $user->name }}">{{ $user->name }}</td>
                <td class="text-truncate" style="max-width: 260px;" title="{{ $user->email }}">{{ $user->email }}</td>
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
                  <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                  </a>

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
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No users found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- MOBILE CARDS (below md) --}}
      <div class="d-md-none">
        @forelse($users as $user)
          <div class="border rounded-3 p-3 mb-3 shadow-sm">
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div class="flex-grow-1">
                <div class="fw-semibold mb-1 text-truncate" title="{{ $user->name }}">
                  <i class="bi bi-person-fill me-1"></i>{{ $user->name }}
                </div>
                <div class="small text-muted text-truncate" title="{{ $user->email }}">
                  <i class="bi bi-envelope-fill me-1"></i>{{ $user->email }}
                </div>
                <div class="small mt-2">
                  <span class="text-muted"><i class="bi bi-telephone-fill me-1"></i>Phone:</span>
                  <span>{{ $user->phone ?? '—' }}</span>
                </div>
                <div class="small mt-2">
                  <span class="text-muted"><i class="bi bi-shield-lock-fill me-1"></i>Roles:</span>
                  @forelse($user->roles as $role)
                    <span class="badge bg-secondary me-1 mt-1">{{ ucfirst($role->name) }}</span>
                  @empty
                    <span class="text-muted">No role</span>
                  @endforelse
                </div>
                <div class="small mt-2">
                  <span class="text-muted"><i class="bi bi-activity me-1"></i>Status:</span>
                  @if($user->is_blocked)
                    <span class="badge text-bg-danger">Blocked</span>
                  @else
                    <span class="badge text-bg-success">Active</span>
                  @endif
                </div>
              </div>

              {{-- Optional avatar placeholder on the right --}}
              <div class="flex-shrink-0">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px;">
                  <i class="bi bi-person text-secondary"></i>
                </div>
              </div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mt-3">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                <i class="bi bi-pencil-fill"></i>
              </a>

              @if(!$user->is_blocked)
                <form action="{{ route('admin.users.block', $user) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-warning"
                    onclick="return confirm('Block this user? They will not be able to log in.')"
                    title="Block">
                    <i class="bi bi-person-slash"></i>
                  </button>
                </form>
              @else
                <form action="{{ route('admin.users.unblock', $user) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success"
                    onclick="return confirm('Unblock this user?')"
                    title="Unblock">
                    <i class="bi bi-person-check"></i>
                  </button>
                </form>
              @endif

              <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"
                  onclick="return confirm('Delete this user?')" title="Delete">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </form>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">No users found.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<style>
  /* Avoid button cramping on very small screens */
  @media (max-width: 575.98px){
    .card-header .btn{padding:.3rem .5rem}
  }
  /* Keep table vertical alignment tidy on md+ */
  @media (min-width: 768px){
    .table td,.table th{vertical-align:middle}
  }
</style>
@endsection
