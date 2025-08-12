@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="card shadow border-0 rounded-3">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0">
        <i class="bi bi-ticket-perforated-fill me-2"></i>
        Tickets
      </h4>
      <a href="{{ route('admin.tickets.create') }}" class="btn btn-light-primary btn-sm d-flex align-items-center">
        <i class="bi bi-plus-circle me-1"></i>
        Add Ticket
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
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th><i class="bi bi-tag-fill me-1"></i>Name</th>
              <th><i class="bi bi-123 me-1"></i>Code</th>
              <th><i class="bi bi-cash-coin me-1"></i>Price (PKR)</th>
              <th><i class="bi bi-stack me-1"></i>Qty</th>
              <th><i class="bi bi-card-text me-1"></i>Notes</th>
              <th><i class="bi bi-clock-history me-1"></i>Created</th>
              <th class="text-center"><i class="bi bi-gear-fill me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tickets as $ticket)
              <tr>
                <td class="fw-semibold">{{ $ticket->name }}</td>
                <td>
                  <span class="badge text-bg-primary">{{ $ticket->code }}</span>
                </td>
                <td>PKR {{ number_format($ticket->price, 2) }}</td>
                <td>1</td>
                <td class="text-muted">
                  {{ \Illuminate\Support\Str::limit($ticket->notes ?? '—', 50) }}
                </td>
                <td>{{ $ticket->created_at->format('d M Y') }}</td>
                <td class="text-center">
                  <a href="{{ route('admin.tickets.edit', $ticket) }}"
                     class="btn btn-sm btn-outline-primary me-1" title="Edit">
                    <i class="bi bi-pencil-fill"></i>
                  </a>

                  <form action="{{ route('admin.tickets.destroy', $ticket) }}"
                        method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Delete this ticket?')"
                            title="Delete">
                      <i class="bi bi-trash-fill"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  No tickets found. <a href="{{ route('admin.tickets.create') }}">Create one</a>.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $tickets->links() }}
      </div>
    </div>
  </div>
</div>
@endsection

<style>
  /* Header gradient & custom accents (matching your users list) */
  .bg-gradient-primary {
    background: linear-gradient(45deg, #0d6efd, #6610f2) !important;
  }
  .btn-light-primary {
    color: #0d6efd;
    background-color: #f0f5ff;
    border: 1px solid #0d6efd;
  }
  .btn-light-primary:hover { background-color: #e2ecff; }
  .table-striped > tbody > tr:nth-of-type(odd) { background-color: rgba(102,16,242,0.05); }
  .table thead th { border-bottom-width: 2px; }
</style>
