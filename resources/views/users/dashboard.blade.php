@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h3 class="mb-1">Welcome, {{ $user->name }}</h3>
      <div class="text-muted">Browse tickets and reserve up to {{ $maxAllowed }}.</div>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="card shadow-sm border-0">
        <div class="card-body py-2 px-3">
          <div class="small text-muted">My Active Purchases</div>
          <div class="fw-bold">{{ $myActivePurchases }} / {{ $maxAllowed }}</div>
        </div>
      </div>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>There was a problem:</strong>
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

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

  @if($tickets->count())
    <div class="row row-cols-xl-4 row-cols-lg-3 row-cols-md-2 row-cols-2 g-4">
      @foreach($tickets as $t)
        @php
          $mine = $t->purchases->first(); // latest purchase by me (if any)
          $hasActiveByAnyone = $t->active_purchase_count > 0;

          $iReachedLimit     = $myActivePurchases >= $maxAllowed;
          $ticketUnavailable = $hasActiveByAnyone && (! $mine || $mine->status !== 'rejected');

          $btnDisabled = $iReachedLimit || $ticketUnavailable;
          $btnText =
            $iReachedLimit ? 'Limit Reached' :
            ($mine?->status === 'pending' ? 'Pending' :
            ($mine?->status === 'accepted' ? 'Purchased' :
            ($mine?->status === 'rejected' ? 'Buy Again' :
            ($ticketUnavailable ? 'Unavailable' : 'Buy Now'))));
        @endphp

        <div class="col">
          <div class="ticket-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="ticket-media">
              @if($t->image_path)
                <img src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                     alt="{{ $t->name }}" class="w-100 h-100 object-cover">
              @else
                <div class="no-image w-100 h-100 d-flex align-items-center justify-content-center">
                  <i class="bi bi-image text-muted fs-1"></i>
                </div>
              @endif
              <div class="ticket-serial badge bg-dark text-white">{{ $t->serial }}</div>
            </div>

            <div class="p-3">
              <div class="fw-semibold mb-2">{{ $t->name }}</div>

              @if($mine)
                <div class="mb-2">
                  @if($mine->status === 'pending')
                    <span class="badge text-bg-warning">Pending</span>
                  @elseif($mine->status === 'accepted')
                    <span class="badge text-bg-success">Accepted</span>
                  @elseif($mine->status === 'rejected')
                    <span class="badge text-bg-danger">Rejected</span>
                  @endif
                </div>
              @endif

              {{-- Use data attributes so Bootstrap opens the modal automatically --}}
              <button type="button"
                      class="btn btn-primary w-100 open-buy-modal"
                      data-bs-toggle="modal"
                      data-bs-target="#buyModal"
                      data-ticket-id="{{ $t->id }}"
                      data-ticket-serial="{{ $t->serial }}"
                      data-ticket-name="{{ $t->name }}"
                      {{ $btnDisabled ? 'disabled' : '' }}>
                {{ $btnText }}
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $tickets->links() }}
    </div>
  @else
    <div class="text-muted">No tickets available yet.</div>
  @endif
</div>

<style>
  .ticket-card{background:#fff}
  .ticket-media{position:relative;height:180px;background:#f8f9fa}
  .object-cover{object-fit:cover}
  .ticket-serial{
    position:absolute;top:10px;left:10px;
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,"Courier New",monospace;letter-spacing:1px
  }
  .no-image{background:#f1f3f5}
</style>

{{-- BUY MODAL --}}
<div class="modal fade" id="buyModal" tabindex="-1" aria-labelledby="buyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="buyForm" method="POST" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="buyModalLabel"><i class="bi bi-bag-plus me-2"></i>Buy Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="ticket_id" id="buy_ticket_id">

        <div class="mb-3">
          <label class="form-label fw-semibold">Ticket Serial</label>
          <input type="text" class="form-control" id="buy_ticket_serial" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Your Name</label>
          <input type="text" class="form-control" value="{{ $user->name }}" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Phone</label>
          <input type="tel"
                 name="phone"
                 id="buy_phone"
                 class="form-control"
                 value="{{ old('phone', $user->phone) }}"
                 placeholder="+92 300 1234567"
                 pattern="^\+?[0-9\s\-()]{7,20}$">
          <div class="form-text">Enter a reachable number (e.g., +92 300 1234567)</div>
        </div>
{{-- Upload proof (REQUIRED) --}}
<div class="mb-3">
  <label class="form-label fw-semibold">Upload Proof of Payment <span class="text-danger">*</span></label>
  <input type="file"
         name="proof"
         id="buy_proof"
         class="form-control"
         accept=".jpg,.jpeg,.png,.webp"
         required>
  <div class="form-text">Required. JPG/PNG/WEBP, max 2MB.</div>
  <img id="buy_preview" alt="" class="mt-2 d-none rounded border" style="max-height: 140px;">
</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Confirm Purchase</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const modalEl     = document.getElementById('buyModal');
    const form        = document.getElementById('buyForm');
    const idInput     = document.getElementById('buy_ticket_id');
    const serialInput = document.getElementById('buy_ticket_serial');
    const proofInput  = document.getElementById('buy_proof');
    const previewImg  = document.getElementById('buy_preview');

    // Populate fields when Bootstrap is about to show the modal
    modalEl.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const id     = button.getAttribute('data-ticket-id');
      const serial = button.getAttribute('data-ticket-serial');

      form.action       = "{{ route('users.tickets.buy', ['ticket' => '__ID__']) }}".replace('__ID__', id);
      idInput.value     = id;
      serialInput.value = serial;

      // reset preview each time
      proofInput.value = '';
      previewImg.classList.add('d-none');
      previewImg.removeAttribute('src');
    });

    // Live image preview
    proofInput.addEventListener('change', ()=>{
      const f = proofInput.files && proofInput.files[0];
      if(!f){ previewImg.classList.add('d-none'); return; }
      const reader = new FileReader();
      reader.onload = e => { previewImg.src = e.target.result; previewImg.classList.remove('d-none'); };
      reader.readAsDataURL(f);
    });

    // If server set a ticket id to reopen modal (after validation error)
    @if(session('buy_ticket_id'))
      const autoBtn = document.querySelector('[data-ticket-id="{{ session('buy_ticket_id') }}"]');
      if (autoBtn) autoBtn.click();
    @endif
  });
</script>
@endsection
