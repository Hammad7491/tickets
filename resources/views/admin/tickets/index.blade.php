{{-- resources/views/admin/tickets/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card border-0 shadow rounded-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bi bi-ticket-perforated me-2"></i>Tickets
      </h5>
      <a href="{{ route('admin.tickets.create') }}" class="btn btn-light btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add Ticket
      </a>
    </div>

    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- TABLE (md and up) --}}
      <div class="table-responsive d-none d-md-block">
        <table class="table align-middle table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Name</th>
              <th>Total Tickets</th>
              <th>Image</th>
              <th>Created</th>
              <th class="text-center" style="width:120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tickets as $t)
              <tr>
                <td class="fw-semibold text-truncate" style="max-width: 280px;" title="{{ $t->name }}">
                  {{ $t->name }}
                </td>

                <td class="text-nowrap">
                  {{ number_format((int) $t->quantity) }}
                </td>

                <td>
                  @if($t->image_path)
                    <div class="d-flex align-items-center gap-2">
                      {{-- Preview in new tab --}}
                      <a href="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                         target="_blank" rel="noopener" class="d-inline-block" title="Open image">
                        <img
                          src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                          alt="Ticket image"
                          class="rounded border"
                          style="width:60px;height:40px;object-fit:cover"
                        >
                      </a>

                      {{-- Download --}}
                      <a href="{{ route('admin.tickets.download', ['path' => $t->image_path]) }}"
                         class="btn btn-sm btn-outline-secondary" title="Download image">
                        <i class="bi bi-download"></i>
                      </a>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td>{{ optional($t->created_at)->format('d M Y') }}</td>

                <td class="text-center">
                  <div class="d-inline-flex gap-1">
                    {{-- Edit --}}
                    <a href="{{ route('admin.tickets.edit', $t->id) }}"
                       class="btn btn-sm btn-outline-primary"
                       title="Edit Ticket">
                      <i class="bi bi-pencil"></i>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('admin.tickets.destroy', $t->id) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="btn btn-sm btn-outline-danger"
                              title="Delete Ticket">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No tickets yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- MOBILE CARDS (below md) --}}
      <div class="d-md-none">
        @forelse($tickets as $t)
          <div class="border rounded-3 p-3 mb-3 shadow-sm">
            <div class="d-flex align-items-start justify-content-between gap-3">
              <div class="flex-grow-1">
                <div class="fw-semibold mb-1 text-truncate" title="{{ $t->name }}">{{ $t->name }}</div>
                <div class="small text-muted mb-2">
                  <span class="fw-semibold">Total Tickets:</span>
                  {{ number_format((int) $t->quantity) }}
                </div>
              </div>

              <div class="flex-shrink-0">
                @if($t->image_path)
                  <a href="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                     target="_blank" rel="noopener" title="Open image">
                    <img
                      src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                      alt="Ticket image"
                      class="rounded border d-block"
                      style="width:84px;height:56px;object-fit:cover"
                    >
                  </a>
                @else
                  <div class="rounded border d-flex align-items-center justify-content-center"
                       style="width:84px;height:56px;background:#f8f9fa;">
                    <span class="text-muted small">No Image</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3">
              <div class="small text-muted">{{ optional($t->created_at)->format('d M Y') }}</div>

              <div class="d-inline-flex gap-2">
                <a href="{{ route('admin.tickets.edit', $t->id) }}"
                   class="btn btn-sm btn-outline-primary"
                   title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>

                @if($t->image_path)
                  <a href="{{ route('admin.tickets.download', ['path' => $t->image_path]) }}"
                     class="btn btn-sm btn-outline-secondary"
                     title="Download">
                    <i class="bi bi-download"></i>
                  </a>
                @else
                  <button type="button"
                          class="btn btn-sm btn-outline-secondary disabled"
                          aria-disabled="true" title="No image to download">
                    <i class="bi bi-download"></i>
                  </button>
                @endif

                <form action="{{ route('admin.tickets.destroy', $t->id) }}"
                      method="POST"
                      onsubmit="return confirm('Delete this ticket?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center text-muted py-4">No tickets yet.</div>
        @endforelse
      </div>

      <div class="mt-3">
        {{ $tickets->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  /* Prevent tiny screens from squashing the header buttons */
  @media (max-width: 575.98px){
    .card-header .btn{padding:.3rem .5rem}
  }
  /* Tweak table cells a bit on md+ */
  @media (min-width: 768px){
    .table td,.table th{vertical-align:middle}
  }
</style>
@endsection
