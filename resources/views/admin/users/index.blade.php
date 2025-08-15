@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-people-fill me-2"></i> Users List
      </h4>
      <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm d-flex align-items-center">
        <i class="bi bi-person-plus-fill me-1"></i> Add New User
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

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 responsive-table">
          <thead class="table-light d-none d-md-table-header-group">
            <tr>
              <th><i class="bi bi-person-fill me-1"></i> Name</th>
              <th><i class="bi bi-envelope-fill me-1"></i> Email</th>
              <th><i class="bi bi-telephone-fill me-1"></i> Phone</th>
              <th><i class="bi bi-shield-lock-fill me-1"></i> Roles</th>
              <th><i class="bi bi-activity me-1"></i> Status</th>
              <th class="text-center"><i class="bi bi-gear-fill me-1"></i> Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr class="bg-white">
                <td class="fw-semibold" data-label="Name">
                  {{ $user->name }}
                </td>
                <td data-label="Email">
                  {{ $user->email }}
                </td>
                <td data-label="Phone">
                  {{ $user->phone ?? '—' }}
                </td>
                <td data-label="Roles">
                  @if($user->roles->isNotEmpty())
                    <span class="badge text-bg-primary rounded-pill">
                      {{ ucfirst($user->roles->first()->name) }}
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td data-label="Status">
                  @if($user->is_blocked)
                    <span class="badge text-bg-danger">Blocked</span>
                  @else
                    <span class="badge text-bg-success">Active</span>
                  @endif
                </td>

                <td class="text-center" data-label="Actions">
                  {{-- Desktop / Tablet: inline buttons --}}
                  <div class="d-none d-md-inline-flex align-items-center gap-1">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                      <i class="bi bi-pencil-fill"></i>
                    </a>

                    @if(!$user->is_blocked)
                      <form action="{{ route('admin.users.block', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Block"
                          onclick="return confirm('Block this user? They will be logged out.')">
                          <i class="bi bi-person-slash"></i>
                        </button>
                      </form>
                    @else
                      <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success" title="Unblock"
                          onclick="return confirm('Unblock this user?')">
                          <i class="bi bi-person-check"></i>
                        </button>
                      </form>
                    @endif

                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                        onclick="return confirm('Are you sure you want to delete this user?')">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </div>

                  {{-- Mobile: compact dropdown --}}
                  <div class="dropdown d-inline-block d-md-none">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('admin.users.edit', $user) }}">
                          <i class="bi bi-pencil-fill"></i> Edit
                        </a>
                      </li>

                      <li><hr class="dropdown-divider"></li>

                      @if(!$user->is_blocked)
                        <li>
                          <form action="{{ route('admin.users.block', $user) }}" method="POST" onsubmit="return confirm('Block this user? They will be logged out.')" class="px-3 py-1">
                            @csrf
                            <button type="submit" class="dropdown-item px-0 d-flex align-items-center gap-2">
                              <i class="bi bi-person-slash"></i> Block
                            </button>
                          </form>
                        </li>
                      @else
                        <li>
                          <form action="{{ route('admin.users.unblock', $user) }}" method="POST" onsubmit="return confirm('Unblock this user?')" class="px-3 py-1">
                            @csrf
                            <button type="submit" class="dropdown-item px-0 d-flex align-items-center gap-2">
                              <i class="bi bi-person-check"></i> Unblock
                            </button>
                          </form>
                        </li>
                      @endif

                      <li><hr class="dropdown-divider"></li>

                      <li>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')"
                              class="px-3 py-1">
                          @csrf @method('DELETE')
                          <button type="submit" class="dropdown-item px-0 d-flex align-items-center gap-2 text-danger">
                            <i class="bi bi-trash-fill"></i> Delete
                          </button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Inline CSS keeps it self-contained; move to your CSS file if preferred --}}
<style>
  /* Stacked cards below md (Bootstrap breakpoint ~768px) */
  @media (max-width: 767.98px) {
    .responsive-table thead { display: none; }
    .responsive-table tbody, 
    .responsive-table tr, 
    .responsive-table td { display: block; width: 100%; }

    .responsive-table tr {
      margin-bottom: 1rem;
      border: 1px solid #e9ecef;
      border-radius: .75rem;
      overflow: hidden;
      box-shadow: 0 1px 2px rgba(16,24,40,.04);
    }

    .responsive-table td {
      padding: .75rem 1rem;
      border: 0 !important;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
    }

    .responsive-table td + td {
      border-top: 1px solid #f1f3f5 !important;
    }

    .responsive-table td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      flex: 0 0 45%;
      max-width: 45%;
      text-align: left;
    }

    .responsive-table td[data-label="Actions"] {
      justify-content: flex-end;
    }
    .responsive-table td[data-label="Actions"]::before {
      content: "";
      display: none; /* actions have their own UI */
    }
  }

  /* Make long strings break nicely on small screens */
  .responsive-table td { word-break: break-word; }
</style>
@endsection
