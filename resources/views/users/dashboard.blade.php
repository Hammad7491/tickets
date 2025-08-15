@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h3 class="mb-1">Welcome, {{ $user->name }}</h3>
      <div class="text-muted">Browse tickets and reserve while stock lasts.</div>
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
          // Controller provided: withCount(['purchases as held_count' => fn($q)=>$q->whereIn('status',['pending','accepted'])])
          $held = (int)($t->held_count ?? 0);
          $qty  = (int)$t->quantity;
          $remaining = max(0, $qty - $held);
        @endphp

        <div class="col">
          <div class="ticket-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
            {{-- Media --}}
            <div class="ticket-media">
              <div class="ticket-media-box">
                @if($t->image_path)
                  <img src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}" alt="{{ $t->name }}">
                @else
                  <div class="no-image">
                    <i class="bi bi-image text-muted fs-1"></i>
                    <div class="small text-muted">No Image</div>
                  </div>
                @endif
              </div>
            </div>

            <div class="p-3 d-flex flex-column gap-2">
              <div class="fw-semibold">{{ $t->name }}</div>

              <div class="d-flex align-items-center justify-content-between">
                @if($remaining > 0)
                  <span class="badge bg-success-subtle text-success fw-bold">Remaining: {{ number_format($remaining) }}</span>
                @else
                  <span class="badge bg-danger-subtle text-danger fw-bold">Out of stock</span>
                @endif

                <button type="button"
                        class="btn btn-primary btn-sm open-buy-modal"
                        data-bs-toggle="modal"
                        data-bs-target="#buyModal"
                        data-ticket-id="{{ $t->id }}"
                        data-ticket-name="{{ $t->name }}"
                        {{ $remaining > 0 ? '' : 'disabled' }}>
                  Buy
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $tickets->links() }}
    </div>
  @else
    <div class="text-muted">No tickets available right now.</div>
  @endif
</div>

<style>
  /* Serial is hidden everywhere (if any old markup leaks it) */
  .ticket-serial{ display:none !important; }

  .ticket-card{ background:#fff; }

  /* Media container */
  .ticket-media{
    position:relative;
    padding:12px;
    background:#f8f9fa;
  }
  .ticket-media-box{
    height:180px;
    border-radius:16px;
    overflow:hidden;
    box-shadow:inset 0 0 0 1px rgba(0,0,0,.06);
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
  }
  .ticket-media-box img{
    max-width:100%;
    max-height:100%;
    width:auto;
    height:auto;
    object-fit:contain;
    object-position:center;
    display:block;
  }
  .no-image{
    height:100%;
    width:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:.25rem;
    border:1px dashed #cbd5e1;
    border-radius:16px;
    background:#fff;
  }

  /* soft badges */
  .badge.bg-success-subtle{ background:#e9f9ef!important; color:#057a55!important; }
  .badge.bg-danger-subtle{  background:#fdebec!important; color:#b42318!important; }
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
          <label class="form-label fw-semibold">Your Name</label>
          <input type="text" class="form-control" value="{{ $user->name }}" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
          <input type="text"
                 name="account_number"
                 id="buy_account_number"
                 class="form-control"
                 value="{{ old('account_number') }}"
                 maxlength="40"
                 placeholder="e.g., 123456789012 / IBAN"
                 required>
          <div class="form-text">Enter your account number (IBAN or numeric). Max 40 chars.</div>
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

        {{-- Proof (required on UI; controller tolerates nullable if you change your mind) --}}
        <div class="mb-3">
          <label class="form-label fw-semibold">Upload Proof of Payment <span class="text-danger">*</span></label>
          <input type="file"
                 name="proof"
                 id="buy_proof"
                 class="form-control"
                 accept=".jpg,.jpeg,.png,.webp"
                 required>
          <div class="form-text">JPG/PNG/WEBP, max 2MB.</div>
          <img id="buy_preview" alt="" class="mt-2 d-none rounded border" style="max-height: 140px;">
        </div>
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
    const proofInput  = document.getElementById('buy_proof');
    const previewImg  = document.getElementById('buy_preview');

    modalEl.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const id = button.getAttribute('data-ticket-id');

      form.action   = "{{ route('users.tickets.buy', ['ticket' => '__ID__']) }}".replace('__ID__', id);
      idInput.value = id;

      // reset preview each time
      proofInput.value = '';
      previewImg.classList.add('d-none');
      previewImg.removeAttribute('src');
    });

    proofInput.addEventListener('change', ()=>{
      const f = proofInput.files && proofInput.files[0];
      if(!f){ previewImg.classList.add('d-none'); return; }
      const reader = new FileReader();
      reader.onload = e => { previewImg.src = e.target.result; previewImg.classList.remove('d-none'); };
      reader.readAsDataURL(f);
    });

    @if(session('buy_ticket_id'))
      const autoBtn = document.querySelector('[data-ticket-id="{{ session('buy_ticket_id') }}"]');
      if (autoBtn) autoBtn.click();
    @endif
  });
</script>
@endsection
