@extends('layouts.app')

@section('content')
<div class="container my-5">

  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <h4 class="mb-0">My Ticket Requests</h4>
    <span class="text-muted">Only your own submissions are shown here.</span>
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

  {{-- DESKTOP/TABLET --}}
  <div class="table-responsive d-none d-md-block">
    <table class="table align-middle table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Ticket</th>
          <th>Account #</th>
          <th>Phone</th>
          <th>Proof</th>
          <th>Serial</th>
          <th>Status</th>
          <th>Requested</th>
        </tr>
      </thead>
      <tbody>
        @forelse($purchases as $p)
          @php
            $serial      = $p->status === 'accepted' ? ($p->serial ?? '—') : '—';
            $showUrl     = route('users.ticketstatus.proof.show', $p);
            $downloadUrl = route('users.ticketstatus.proof.download', $p);
          @endphp
          <tr>
            <td class="fw-semibold">{{ $p->ticket->name ?? 'Ticket' }}</td>

            <td class="font-monospace">{{ $p->account_number }}</td>
            <td>{{ $p->phone ?? '—' }}</td>

            <td>
              @if($p->proof_image_path)
                <div class="d-flex align-items-center gap-2">
                  <a href="{{ $showUrl }}" target="_blank" rel="noopener" class="d-inline-block" title="Open proof">
                    <img src="{{ $showUrl }}" alt="Proof" class="rounded border" style="width:60px;height:40px;object-fit:cover">
                  </a>
                  <a href="{{ $downloadUrl }}" class="btn btn-sm btn-outline-secondary" title="Download proof">
                    <i class="bi bi-download"></i>
                  </a>
                </div>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>

            <td class="font-monospace">{{ $serial }}</td>

            <td>
              @if($p->status === 'pending')
                <span class="badge text-bg-warning">Pending</span>
              @elseif($p->status === 'accepted')
                <span class="badge text-bg-success">Accepted</span>
              @else
                <span class="badge text-bg-danger">Rejected</span>
              @endif
            </td>

            <td>{{ $p->created_at?->format('d M Y') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">No requests yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="d-md-none">
    @forelse($purchases as $p)
      @php
        $serial      = $p->status === 'accepted' ? ($p->serial ?? '—') : '—';
        $showUrl     = $p->proof_image_path ? route('users.ticketstatus.proof.show', $p) : null;
        $downloadUrl = $p->proof_image_path ? route('users.ticketstatus.proof.download', $p) : null;
      @endphp
      <div class="border rounded-3 p-3 mb-3 shadow-sm">
        <div class="d-flex justify-content-between gap-3">
          <div class="flex-grow-1">
            <div class="fw-semibold text-truncate" title="{{ $p->ticket->name ?? 'Ticket' }}">
              {{ $p->ticket->name ?? 'Ticket' }}
            </div>
            <div class="small text-muted mt-1">
              <div><span class="fw-semibold">Account:</span> <span class="font-monospace">{{ $p->account_number }}</span></div>
              <div><span class="fw-semibold">Phone:</span> {{ $p->phone ?? '—' }}</div>
              <div><span class="fw-semibold">Serial:</span> <span class="font-monospace">{{ $serial }}</span></div>
            </div>
          </div>
          <div class="flex-shrink-0">
            @if($showUrl)
              <a href="{{ $showUrl }}" target="_blank" rel="noopener" title="Open proof">
                <img src="{{ $showUrl }}" alt="Proof"
                     class="rounded border d-block" style="width:84px;height:56px;object-fit:cover">
              </a>
            @else
              <div class="rounded border d-flex align-items-center justify-content-center"
                   style="width:84px;height:56px;background:#f8f9fa;">
                <span class="text-muted small">No Proof</span>
              </div>
            @endif
          </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3">
          <div class="small text-muted">{{ $p->created_at?->format('d M Y') }}</div>
          <div>
            @if($p->status === 'pending')
              <span class="badge text-bg-warning">Pending</span>
            @elseif($p->status === 'accepted')
              <span class="badge text-bg-success">Accepted</span>
            @else
              <span class="badge text-bg-danger">Rejected</span>
            @endif
          </div>
        </div>

        @if($downloadUrl)
          <div class="mt-2">
            <a href="{{ $downloadUrl }}" class="btn btn-sm btn-outline-secondary w-100">
              <i class="bi bi-download me-1"></i> Download Proof
            </a>
          </div>
        @endif
      </div>
    @empty
      <div class="text-center text-muted py-4">No requests yet.</div>
    @endforelse
  </div>

  <div class="mt-3">{{ $purchases->links() }}</div>
</div>

<style>
  .font-monospace{
    font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace
  }
</style>
@endsection
