{{-- resources/views/admin/reviews/accepted.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

  {{-- Page header --}}
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h1 class="fw-bold display-6 mb-1">Accepted Ticket Purchases</h1>
      <div class="text-muted">Purchases that have been approved.</div>
    </div>

    <div class="d-flex align-items-center gap-2">
      {{-- Bulk delete (triggers hidden form) --}}
      <button id="deleteSelectedBtn" type="button" class="btn btn-outline-danger d-flex align-items-center gap-2">
        <i class="bi bi-trash"></i>
        Delete Selected
      </button>

      {{-- Link to pending list --}}
      <a href="{{ route('admin.reviews.pending') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-hourglass-split"></i>
        View Pending
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

  {{-- Table --}}
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0 responsive-table">
          <thead class="table-light">
            <tr>
              <th style="width:44px">
                <input type="checkbox" id="selectAll" class="form-check-input">
              </th>
              <th style="width:56px">#</th>
              <th>User Name</th>
              <th>Phone Number</th>
              <th>Ticket Name</th>
              <th>Serial Number</th>
              <th>Account Number</th>
              <th>Proof</th>
              <th>Date</th>
              <th class="text-end" style="width:80px">Action</th>
            </tr>
          </thead>

          <tbody>
            @forelse($purchases as $idx => $p)
              @php
                $rowNum      = ($purchases->currentPage() - 1) * $purchases->perPage() + $idx + 1;
                $ticket      = $p->ticket;
                $user        = $p->user;
                $previewUrl  = $p->proof_image_path ? route('admin.reviews.proof.show', $p->id)     : null;
                $downloadUrl = $p->proof_image_path ? route('admin.reviews.proof.download', $p->id) : null;
              @endphp

              <tr class="bg-white">
                {{-- Row checkbox --}}
                <td data-label="Select">
                  <input type="checkbox" class="form-check-input row-check" value="{{ $p->id }}">
                </td>

                <td class="text-muted" data-label="#"> {{ $rowNum }} </td>

                <td data-label="User Name">
                  <div class="fw-semibold">{{ $user?->name ?? '—' }}</div>
                </td>

                <td data-label="Phone Number">
                  <div class="small">{{ $p->phone ?? $user?->phone ?? '—' }}</div>
                </td>

                <td data-label="Ticket Name">
                  <div class="fw-semibold">{{ $ticket?->name ?? '—' }}</div>
                </td>

                <td class="font-monospace" data-label="Serial Number">
                  {{ $p->serial ?? '—' }}
                </td>

                <td class="font-monospace" data-label="Account Number">
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

                <td class="small text-muted" data-label="Date">
                  {{ $p->updated_at?->format('d M Y, h:i A') }}
                </td>

                {{-- Action: single delete (uses hidden form) --}}
                <td class="text-end" data-label="Action">
                  <button type="button"
                          class="btn btn-sm btn-outline-danger js-delete-one"
                          data-id="{{ $p->id }}"
                          title="Delete this row">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted py-4">No accepted purchases yet.</td>
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

{{-- Hidden forms --}}
<form id="singleDeleteForm" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<form id="bulkDeleteForm" action="{{ route('admin.reviews.bulkDelete') }}" method="POST" style="display:none;">
  @csrf
  <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

{{-- Styles --}}
<style>
  .font-monospace{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  }

  .display-6{
    font-size: clamp(1.5rem, 2.2vw + 1rem, 2.5rem);
  }

  .proof-thumb{ width: 72px !important; height: 48px !important; }

  /* Mobile stacked-cards layout */
  @media (max-width: 767.98px){
    .responsive-table thead { display: none !important; }
    .responsive-table tbody,
    .responsive-table tr,
    .responsive-table td { display: block; width: 100%; }

    .responsive-table tr{
      margin-bottom: 1rem;
      border: 1px solid #e9ecef;
      border-radius: .75rem;
      overflow: hidden;
      box-shadow: 0 1px 2px rgba(16,24,40,.04);
      padding-top: .25rem;
    }

    .responsive-table td{
      padding: .75rem 1rem;
      border: 0 !important;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
      word-break: break-word;
    }

    .responsive-table td + td{
      border-top: 1px solid #f1f3f5 !important;
    }

    .responsive-table td::before{
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      flex: 0 0 48%;
      max-width: 48%;
      text-align: left;
    }

    .proof-thumb{ width: 84px !important; height: 56px !important; }
    .text-end { text-align: right !important; }
  }
</style>

{{-- Scripts --}}
<script>
  // ----- SINGLE ROW DELETE -----
  (function () {
    const form = document.getElementById('singleDeleteForm');
    if (!form) return;

    document.querySelectorAll('.js-delete-one').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        if (!id) return;

        if (!confirm('Delete this purchase?')) return;

        const urlTemplate = @json(route('admin.reviews.destroy', ':id'));
        form.action = urlTemplate.replace(':id', id);
        form.submit();
      });
    });
  })();

  // ----- BULK DELETE -----
  (function () {
    const selectAll = document.getElementById('selectAll');
    const rowChecks = document.querySelectorAll('.row-check');
    const bulkBtn   = document.getElementById('deleteSelectedBtn');
    const bulkForm  = document.getElementById('bulkDeleteForm');
    const bulkIds   = document.getElementById('bulkDeleteIds');

    if (selectAll) {
      selectAll.addEventListener('change', () => {
        rowChecks.forEach(c => c.checked = selectAll.checked);
      });
    }

    bulkBtn?.addEventListener('click', () => {
      const ids = [...rowChecks].filter(c => c.checked).map(c => c.value);
      if (!ids.length) { alert('Select at least one row.'); return; }
      if (!confirm(`Delete ${ids.length} selected purchase(s)?`)) return;
      bulkIds.value = ids.join(',');
      bulkForm.submit();
    });
  })();
</script>
@endsection
