@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">Accepted Ticket Purchases</h2>
      <div class="text-muted">Purchases that have been approved.</div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.reviews.pending') }}" class="btn btn-light">
        <i class="bi bi-hourglass-split me-1"></i> View Pending
      </a>
    </div>
  </div>

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
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Contact</th>
              <th>Ticket</th>
              <th>Serial</th>
              <th>Account #</th>
              <th>Proof</th>
              <th>Accepted At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($purchases as $idx => $p)
              @php
                $rowNum = ($purchases->currentPage() - 1) * $purchases->perPage() + $idx + 1;
                $ticket = $p->ticket;
                $user   = $p->user;

                $previewUrl  = $p->proof_image_path ? route('admin.reviews.proof.show', $p->id)     : null;
                $downloadUrl = $p->proof_image_path ? route('admin.reviews.proof.download', $p->id) : null;
              @endphp
              <tr>
                <td class="text-muted">{{ $rowNum }}</td>

                <td>
                  <div class="fw-semibold">{{ $user?->name ?? '—' }}</div>
                  <div class="small text-muted">{{ $user?->email ?? '—' }}</div>
                </td>

                <td><div class="small">{{ $p->phone ?? $user?->phone ?? '—' }}</div></td>

                <td>
                  <div class="fw-semibold">{{ $ticket?->name ?? '—' }}</div>
                  <div class="small text-muted">ID: {{ $ticket?->id ?? '—' }}</div>
                </td>

                {{-- SERIAL NOW FROM PURCHASE --}}
                <td class="font-monospace">{{ $p->serial ?? '—' }}</td>

                <td class="text-monospace">{{ $p->account_number ?? '—' }}</td>

                <td>
                  @if($previewUrl)
                    <div class="d-flex align-items-center gap-2">
                      <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="d-inline-block" title="Open proof">
                        <img src="{{ $previewUrl }}" alt="Proof" class="rounded border" style="width:60px;height:40px;object-fit:cover">
                      </a>
                      <a href="{{ $downloadUrl }}" class="btn btn-sm btn-outline-secondary" title="Download">
                        <i class="bi bi-download"></i>
                      </a>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td class="small text-muted">{{ $p->updated_at?->format('d M Y, h:i A') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No accepted purchases yet.</td>
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
  .text-monospace,.font-monospace{
    font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
  }
</style>
@endsection
