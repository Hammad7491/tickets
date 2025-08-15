{{-- resources/views/admin/reviews/pending.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">Pending Ticket Purchases</h2>
      <div class="text-muted">All user submissions that require admin review.</div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.tickets.index', ['status' => 'accepted']) }}" class="btn btn-light">
        <i class="bi bi-check2-circle me-1"></i> View Accepted
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0 responsive-table">
          <thead class="table-light d-none d-md-table-header-group">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Contact</th>
              <th>Ticket</th>
              <th>Serial</th>
              <th>Account #</th>
              <th>Proof</th>
              <th>Submitted</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($purchases as $idx => $p)
              @php
                $rowNum  = ($purchases->currentPage() - 1) * $purchases->perPage() + $idx + 1;
                $ticket  = $p->ticket;
                $user    = $p->user;

                $previewUrl  = $p->proof_image_path ? route('admin.reviews.proof.show', $p->id)     : null;
                $downloadUrl = $p->proof_image_path ? route('admin.reviews.proof.download', $p->id) : null;
              @endphp

              <tr class="bg-white">
                <td class="text-muted" data-label="#">
                  {{ $rowNum }}
                </td>

                <td data-label="User">
                  <div class="fw-semibold">{{ $user?->name ?? '—' }}</div>
                  <div class="small text-muted">{{ $user?->email ?? '—' }}</div>
                </td>

                <td data-label="Contact">
                  <div class="small">{{ $p->phone ?? $user?->phone ?? '—' }}</div>
                </td>

                <td data-label="Ticket">
                  <div class="fw-semibold">{{ $ticket?->name ?? '—' }}</div>
                  <div class="small text-muted">ID: {{ $ticket?->id ?? '—' }}</div>
                </td>

                <td class="font-monospace" data-label="Serial">
                  {{ $ticket?->serial ?? '—' }}
                </td>

                <td class="text-monospace" data-label="Account #">
                  {{ $p->account_number ?? '—' }}
                </td>

                <td data-label="Proof">
                  @if($previewUrl)
                    <div class="d-flex align-items-center gap-2">
                      <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="d-inline-block" title="Open proof">
                        <img
                          src="{{ $previewUrl }}"
                          alt="Proof"
                          class="rounded border proof-thumb"
                          style="width:60px;height:40px;object-fit:cover"
                        >
                      </a>
                      <a href="{{ $downloadUrl }}" class="btn btn-sm btn-outline-secondary" title="Download">
                        <i class="bi bi-download"></i>
                      </a>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td class="small text-muted" data-label="Submitted">
                  {{ $p->created_at?->format('d M Y, h:i A') }}
                </td>

                <td class="text-end" data-label="Action">
                  {{-- md+ screens: inline buttons --}}
                  <div class="d-none d-md-inline-flex align-items-center gap-2">
                    <form
                      action="{{ route('admin.reviews.accept', $p->id) }}"
                      method="POST"
                      onsubmit="return confirm('Approve this purchase?');"
                    >
                      @csrf
                      @method('PUT')
                      <button class="btn btn-success btn-sm">
                        <i class="bi bi-check2 me-1"></i> Accept
                      </button>
                    </form>

                    <form
                      action="{{ route('admin.reviews.reject', $p->id) }}"
                      method="POST"
                      onsubmit="return confirm('Reject this purchase?');"
                    >
                      @csrf
                      @method('PUT')
                      <button class="btn btn-danger btn-sm">
                        <i class="bi bi-x-lg me-1"></i> Reject
                      </button>
                    </form>
                  </div>

                  {{-- < md screens: dropdown actions --}}
                  <div class="dropdown d-inline-block d-md-none">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li class="px-3 py-1">
                        <form
                          action="{{ route('admin.reviews.accept', $p->id) }}"
                          method="POST"
                          onsubmit="return confirm('Approve this purchase?');"
                        >
                          @csrf
                          @method('PUT')
                          <button type="submit" class="dropdown-item px-0 d-flex align-items-center gap-2">
                            <i class="bi bi-check2 text-success"></i> Accept
                          </button>
                        </form>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li class="px-3 py-1">
                        <form
                          action="{{ route('admin.reviews.reject', $p->id) }}"
                          method="POST"
                          onsubmit="return confirm('Reject this purchase?');"
                        >
                          @csrf
                          @method('PUT')
                          <button type="submit" class="dropdown-item px-0 d-flex align-items-center gap-2">
                            <i class="bi bi-x-lg text-danger"></i> Reject
                          </button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">
                  No pending purchases right now.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $purchases->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  .text-monospace, .font-monospace{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  }

  /* tighten proof thumb on very small screens */
  @media (max-width: 575.98px){
    .proof-thumb{ width: 72px !important; height: 48px !important; }
  }

  /* Responsive "stacked cards" under md (Bootstrap md ~ 768px) */
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
      padding-top: .25rem;
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

    /* Let multi-line values flow nicely */
    .responsive-table td > *:not(img) { flex: 1 1 auto; }

    /* Action row: keep only the dropdown label */
    .responsive-table td[data-label="Action"] {
      justify-content: flex-end;
    }
    .responsive-table td[data-label="Action"]::before {
      content: ""; display: none;
    }
  }

  /* Word-breaking safety for emails/serials/account numbers */
  .responsive-table td { word-break: break-word; }
</style>
@endsection
