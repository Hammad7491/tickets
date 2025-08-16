@extends('layouts.app')

@section('content')
<div class="container my-5">

  {{-- Page header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
      <span class="page-icon d-inline-flex align-items-center justify-content-center rounded-3">
        <i class="bi bi-receipt-cutoff"></i>
      </span>
      <div>
        <h3 class="mb-1 fw-bold">My Ticket Requests</h3>
       
      </div>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- DESKTOP / TABLET --}}
  <div class="card border-0 shadow-sm rounded-4 d-none d-md-block">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
          <thead>
            <tr>
              <th>Ticket</th>
              <th>Account #</th>
              <th>Phone</th>
              <th>Proof</th>
              <th>Serial</th>
              <th>Status</th>
              <th>Requested</th>
              <th class="text-end" style="width: 100px;">Actions</th>
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
                <td class="fw-semibold text-truncate" title="{{ $p->ticket->name ?? 'Ticket' }}">
                  <i class="bi bi-ticket-perforated me-1 text-primary"></i>{{ $p->ticket->name ?? 'Ticket' }}
                </td>

                <td>
                  <div class="chip chip-mono" data-copy="{{ $p->account_number }}" title="Click to copy">
                    <i class="bi bi-clipboard me-1"></i>
                    <span class="font-monospace">{{ $p->account_number }}</span>
                  </div>
                </td>

                <td class="text-nowrap">{{ $p->phone ?? '—' }}</td>

                <td>
                  @if($p->proof_image_path)
                    <div class="proof-thumb">
                      <a href="{{ $showUrl }}" target="_blank" rel="noopener" class="d-inline-block" title="Open proof">
                        <img src="{{ $showUrl }}" alt="Proof">
                        <span class="overlay"><i class="bi bi-zoom-in"></i></span>
                      </a>
                      <a href="{{ $downloadUrl }}" class="btn btn-light btn-sm ms-2" title="Download proof">
                        <i class="bi bi-download"></i>
                      </a>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>

                <td>
                  <div class="chip chip-mono {{ $serial !== '—' ? 'chip-good' : '' }}"
                       @if($serial !== '—') data-copy="{{ $serial }}" title="Click to copy" @endif>
                    <i class="bi {{ $serial !== '—' ? 'bi-clipboard' : 'bi-dash-lg' }} me-1"></i>
                    <span class="font-monospace">{{ $serial }}</span>
                  </div>
                </td>

                <td>
                  @if($p->status === 'pending')
                    <span class="status-badge badge-soft-warning"><span class="dot"></span> Pending</span>
                  @elseif($p->status === 'accepted')
                    <span class="status-badge badge-soft-success"><span class="dot"></span> Accepted</span>
                  @else
                    <span class="status-badge badge-soft-danger"><span class="dot"></span> Rejected</span>
                  @endif
                </td>

                <td class="text-muted">{{ $p->created_at?->format('d M Y') }}</td>

                <td class="text-end">
                  @if($p->status !== 'accepted')
                    <form action="{{ route('users.ticketstatus.destroy', $p) }}" method="POST"
                          onsubmit="return confirm('Delete this request?');" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @else
                    <button class="btn btn-outline-secondary btn-sm" disabled
                            title="Accepted purchases cannot be deleted">
                      <i class="bi bi-lock"></i>
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="bi bi-inbox me-2"></i>No requests yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="d-md-none mt-4">
    @forelse($purchases as $p)
      @php
        $serial      = $p->status === 'accepted' ? ($p->serial ?? '—') : '—';
        $showUrl     = $p->proof_image_path ? route('users.ticketstatus.proof.show', $p) : null;
        $downloadUrl = $p->proof_image_path ? route('users.ticketstatus.proof.download', $p) : null;
      @endphp

      <div class="card shadow-sm border-0 rounded-4 mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between gap-3">
            <div class="flex-grow-1">
              <div class="fw-semibold text-truncate" title="{{ $p->ticket->name ?? 'Ticket' }}">
                <i class="bi bi-ticket-perforated me-1 text-primary"></i>{{ $p->ticket->name ?? 'Ticket' }}
              </div>

              <div class="small text-muted mt-2">
                <div class="mb-1">
                  <span class="fw-semibold">Account:</span>
                  <span class="chip chip-mono mt-1" data-copy="{{ $p->account_number }}" title="Click to copy">
                    <i class="bi bi-clipboard me-1"></i><span class="font-monospace">{{ $p->account_number }}</span>
                  </span>
                </div>
                <div class="mb-1"><span class="fw-semibold">Phone:</span> {{ $p->phone ?? '—' }}</div>
                <div class="mb-1">
                  <span class="fw-semibold">Serial:</span>
                  <span class="chip chip-mono {{ $serial !== '—' ? 'chip-good' : '' }}"
                        @if($serial !== '—') data-copy="{{ $serial }}" title="Click to copy" @endif>
                    <i class="bi {{ $serial !== '—' ? 'bi-clipboard' : 'bi-dash-lg' }} me-1"></i>
                    <span class="font-monospace">{{ $serial }}</span>
                  </span>
                </div>
              </div>
            </div>

            <div class="flex-shrink-0">
              @if($showUrl)
                <a href="{{ $showUrl }}" target="_blank" rel="noopener" title="Open proof" class="proof-thumb-sm">
                  <img src="{{ $showUrl }}" alt="Proof">
                  <span class="overlay"><i class="bi bi-zoom-in"></i></span>
                </a>
              @else
                <div class="rounded border d-flex align-items-center justify-content-center"
                     style="width:96px;height:64px;background:#f8f9fa;">
                  <span class="text-muted small">No Proof</span>
                </div>
              @endif
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mt-3">
            <div class="small text-muted">{{ $p->created_at?->format('d M Y') }}</div>
            <div>
              @if($p->status === 'pending')
                <span class="status-badge badge-soft-warning"><span class="dot"></span> Pending</span>
              @elseif($p->status === 'accepted')
                <span class="status-badge badge-soft-success"><span class="dot"></span> Accepted</span>
              @else
                <span class="status-badge badge-soft-danger"><span class="dot"></span> Rejected</span>
              @endif
            </div>
          </div>

          <div class="mt-3 d-grid gap-2">
            @if($downloadUrl)
              <a href="{{ $downloadUrl }}" class="btn btn-light border">
                <i class="bi bi-download me-1"></i> Download Proof
              </a>
            @endif

            @if($p->status !== 'accepted')
              <form action="{{ route('users.ticketstatus.destroy', $p) }}" method="POST"
                    onsubmit="return confirm('Delete this request?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">
                  <i class="bi bi-trash me-1"></i> Delete
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="text-center text-muted py-4">
        <i class="bi bi-inbox me-2"></i>No requests yet.
      </div>
    @endforelse
  </div>

  <div class="mt-3">{{ $purchases->links() }}</div>
</div>

<style>
  /* page icon */
  .page-icon{
    width:44px;height:44px;background:#f5f8ff;color:#4f7cff;font-size:20px;
  }

  /* monospace helper */
  .font-monospace{
    font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace
  }

  /* modern table */
  .table-modern thead th{
    background:#f8f9fb;border-bottom:1px solid #eef0f4;
    text-transform:uppercase; font-size:.75rem; letter-spacing:.02em; color:#6b7280;
  }
  .table-modern tbody tr{
    --row-bg:#fff;
    background:var(--row-bg);
  }
  .table-modern tbody tr:hover{
    --row-bg:#f9fbff;
  }
  .table-modern td, .table-modern th{
    padding:14px 16px; vertical-align:middle;
  }

  /* proof thumbnail with hover overlay */
  .proof-thumb img{
    width:72px;height:48px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;display:block;
  }
  .proof-thumb{ position:relative; display:inline-flex; align-items:center; }
  .proof-thumb .overlay{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    border-radius:10px; background:rgba(0,0,0,.2); color:#fff; opacity:0; transition:opacity .18s ease;
  }
  .proof-thumb:hover .overlay{ opacity:1; }

  .proof-thumb-sm{ position:relative; display:inline-block; }
  .proof-thumb-sm img{
    width:96px;height:64px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;display:block;
  }
  .proof-thumb-sm .overlay{
    position:absolute; inset:0; display:flex; align-items:center; justify-content:center;
    border-radius:12px; background:rgba(0,0,0,.25); color:#fff; opacity:0; transition:opacity .18s ease;
  }
  .proof-thumb-sm:hover .overlay{ opacity:1; }

  /* status badges (soft) */
  .status-badge{
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.375rem .6rem; border-radius:999px; font-weight:600; font-size:.75rem;
    border:1px solid transparent;
  }
  .status-badge .dot{ width:.45rem; height:.45rem; border-radius:50%; display:inline-block; }
  .badge-soft-warning{ background:#fff7e6; color:#a15c00; border-color:#ffe6b3; }
  .badge-soft-warning .dot{ background:#f59f00; }
  .badge-soft-success{ background:#e9f9ef; color:#057a55; border-color:#c7f0d6; }
  .badge-soft-success .dot{ background:#12b981; }
  .badge-soft-danger{ background:#fdebec; color:#b42318; border-color:#f7c7c9; }
  .badge-soft-danger .dot{ background:#ef4444; }

  /* chips */
  .chip{
    display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .55rem;
    background:#f5f8ff; border:1px solid #e6ecff; border-radius:10px; cursor:default;
  }
  .chip-mono{ font-size:.9rem; }
  .chip-good{ background:#e9f9ef; border-color:#c7f0d6; }

  /* clickable copy */
  .chip[data-copy]{ cursor:pointer; user-select:none; }
</style>

<script>
  // Copy helper with small visual feedback
  const copyText = async (text, el) => {
    try {
      await navigator.clipboard.writeText(text);
      if (!el) return;
      el.classList.add('btn-success','text-white');
      const icon = el.querySelector('i');
      const old  = icon ? icon.className : '';
      if(icon) icon.className = 'bi bi-clipboard-check';
      setTimeout(()=>{
        el.classList.remove('btn-success','text-white');
        if(icon) icon.className = old || 'bi bi-clipboard';
      }, 1200);
    } catch (e) { console.error(e); }
  };

  document.addEventListener('click', (e) => {
    const chip = e.target.closest('.chip[data-copy]');
    if (chip) copyText(chip.dataset.copy, chip);
  }, { passive:true });
</script>
@endsection
