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

      {{-- Search by Name --}}
      <div class="d-flex justify-content-end mb-3">
        <div class="search-wrap">
          <label for="nameSearch" class="form-label fw-semibold mb-1">Search by Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" id="nameSearch" class="form-control" placeholder="Type a name…" autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clearNameSearch" title="Clear">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
      </div>

      <div id="noNameResults" class="alert alert-info py-2 px-3 d-none" role="alert">
        <i class="bi bi-info-circle me-2"></i>No matching users on this page.
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 responsive-table">
          {{-- ✅ Show headers normally; CSS will hide on small screens --}}
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Role</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>

          <tbody id="usersTbody">
            @foreach($users as $user)
              <tr class="bg-white" data-name="{{ strtolower($user->name ?? '') }}">
                <td class="fw-semibold" data-label="Name">
                  {{ $user->name }}
                </td>
                <td data-label="Email">
                  {{ $user->email }}
                </td>
                <td data-label="Phone Number">
                 {{ $user->phone ?: '—' }}

                </td>
                <td data-label="Role">
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

                <td class="text-center" data-label="Action">
                  {{-- Desktop / Tablet --}}
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

                  {{-- Mobile --}}
                  <div class="dropdown d-inline-block d-md-none">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Action
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

<style>
  .search-wrap{ min-width: 260px; max-width: 360px; width: 100%; }

  /* Hide header on small screens and show stacked rows */
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
      flex: 0 0 50%;
      max-width: 50%;
      text-align: left;
    }

    .responsive-table td[data-label="Action"] {
      justify-content: flex-end;
    }
    .responsive-table td[data-label="Action"]::before {
      content: "";
      display: none;
    }
  }

  .responsive-table td { word-break: break-word; }
</style>

{{-- Realtime filter by name --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const input   = document.getElementById('nameSearch');
    const clear   = document.getElementById('clearNameSearch');
    const rows    = Array.from(document.querySelectorAll('#usersTbody tr[data-name]'));
    const noRes   = document.getElementById('noNameResults');

    function apply() {
      const q = (input.value || '').trim().toLowerCase();
      let shown = 0;

      rows.forEach(tr => {
        const name = (tr.dataset.name || '');
        const show = !q || name.includes(q);
        tr.style.display = show ? '' : 'none';
        if (show) shown++;
      });

      if (noRes) noRes.classList.toggle('d-none', shown !== 0);
    }

    input?.addEventListener('input', apply);
    clear?.addEventListener('click', () => { input.value = ''; apply(); input.focus(); });

    apply(); // initial run
  });
</script>
@endsection
