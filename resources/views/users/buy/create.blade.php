@extends('layouts.app')

@section('content')
@php
  // Payment meta (override in config('payments.*') or .env)
  $epTitle  = config('payments.easypaisa.title',  env('EASYPAY_TITLE',   'Asad Ali'));
  $epNumber = config('payments.easypaisa.number', env('EASYPAY_NUMBER',  '03091223334'));
  $jcTitle  = config('payments.jazzcash.title',   env('JAZZCASH_TITLE',  'Asad Ali'));
  $jcNumber = config('payments.jazzcash.number',  env('JAZZCASH_NUMBER', '03094433221'));

  // ✅ Logos (from your screenshot)
  $epLogo = asset('asset/images/Easypaisa.png');
  $jcLogo = asset('asset/images/Jazzcash.png');

  // Optional: fallback if file missing
  $fallbackLogo = asset('assets/images/logo.png');
@endphp

<div class="container py-5">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h3 class="mb-1">Buy Ticket</h3>
      <div class="text-muted">Transfer payment in any given account & submit proof.</div>
    </div>
    <div>
      <a href="{{ url()->previous() }}" class="btn btn-light">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>There was a problem:</strong>
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Payment accounts (Easypaisa & JazzCash) --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="pay-card d-flex align-items-center gap-3 p-3 rounded-3">
            <img src="{{ $epLogo }}" alt="Easypaisa" class="pay-logo"
                 onerror="this.onerror=null;this.src='{{ $fallbackLogo }}'">
            <div class="flex-grow-1">
              <div class="small text-muted mb-1">Account Title:</div>
              <div class="fw-semibold">{{ $epTitle }}</div>

              <div class="small text-muted mt-2 mb-1">Account Number:</div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="copyable fw-semibold" data-copy="{{ $epNumber }}" title="Click to copy">{{ $epNumber }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm copy-btn" data-copy="{{ $epNumber }}">
                  <i class="bi bi-clipboard me-1"></i> Copy
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="pay-card d-flex align-items-center gap-3 p-3 rounded-3">
            <img src="{{ $jcLogo }}" alt="JazzCash" class="pay-logo"
                 onerror="this.onerror=null;this.src='{{ $fallbackLogo }}'">
            <div class="flex-grow-1">
              <div class="small text-muted mb-1">Account Title:</div>
              <div class="fw-semibold">{{ $jcTitle }}</div>

              <div class="small text-muted mt-2 mb-1">Account Number:</div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="copyable fw-semibold" data-copy="{{ $jcNumber }}" title="Click to copy">{{ $jcNumber }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm copy-btn" data-copy="{{ $jcNumber }}">
                  <i class="bi bi-clipboard me-1"></i> Copy
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <p class="text-center text-muted mt-3 mb-0">
        Transfer payment to <strong>any</strong> of the above accounts and upload proof below.
      </p>
    </div>
  </div>

  {{-- Payment submission form --}}
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <form action="{{ route('users.buy.store', $ticket->id) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="mb-3">
          <label class="form-label fw-semibold">Your Name</label>
          <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
          <input type="text"
                 name="account_number"
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
                 class="form-control"
                 value="{{ old('phone', auth()->user()->phone) }}"
                 placeholder="+92 300 1234567"
                 pattern="^\+?[0-9\s\-()]{7,20}$">
          <div class="form-text">Enter a reachable number (e.g., +92 300 1234567)</div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Upload Proof of Payment <span class="text-danger">*</span></label>
          <input type="file"
                 name="proof"
                 id="proof"
                 class="form-control"
                 accept=".jpg,.jpeg,.png,.webp"
                 required>
          <div class="form-text">JPG/PNG/WEBP, max 2MB.</div>
          <img id="proof_preview" alt="" class="mt-2 d-none rounded border" style="max-height: 160px;">
        </div>

        <div class="d-flex gap-2">
          <a href="{{ url()->previous() }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2 me-1"></i> Confirm Purchase
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .pay-card{
    background:#f5f8ff;
    border:1px solid #e6ecff;
  }

  /* Bigger, clearer logos */
  .pay-logo{
    width:96px;               /* ⬆ was 48px */
    height:96px;              /* ⬆ was 48px */
    object-fit:contain;
    border-radius:50%;
    background:#fff;
    padding:10px;             /* a bit more inner padding */
    border:1px solid #eef0f4;
    box-shadow:0 1px 2px rgba(0,0,0,.06);
    image-rendering:-webkit-optimize-contrast;
  }

  /* Scale down gracefully on smaller screens */
  @media (max-width: 991.98px){
    .pay-logo{ width:80px; height:80px; }
  }
  @media (max-width: 575.98px){
    .pay-logo{ width:64px; height:64px; }
  }

  .copyable{ cursor:pointer; user-select:none; }
</style>


<script>
  document.addEventListener('DOMContentLoaded', function(){
    // Proof preview
    const input = document.getElementById('proof');
    const img   = document.getElementById('proof_preview');
    if(input && img){
      input.addEventListener('change', ()=>{
        const f = input.files && input.files[0];
        if(!f){ img.classList.add('d-none'); img.removeAttribute('src'); return; }
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.classList.remove('d-none'); };
        reader.readAsDataURL(f);
      });
    }

    // Copy helpers
    const doCopy = async (text, btnOrSpan) => {
      try{
        await navigator.clipboard.writeText(text);
        if(btnOrSpan){
          const icon = btnOrSpan.querySelector('i');
          const old  = icon ? icon.className : '';
          if(icon){ icon.className = 'bi bi-clipboard-check'; }
          btnOrSpan.classList.add('btn-success','text-white');
          setTimeout(()=>{
            if(icon){ icon.className = old || 'bi bi-clipboard'; }
            btnOrSpan.classList.remove('btn-success','text-white');
          }, 1400);
        }
      }catch(e){ console.error('Copy failed', e); }
    };

    document.querySelectorAll('.copy-btn').forEach(btn=>{
      btn.addEventListener('click', ()=> doCopy(btn.dataset.copy, btn));
    });
    document.querySelectorAll('.copyable').forEach(span=>{
      span.addEventListener('click', ()=> doCopy(span.dataset.copy, span.closest('.d-flex')?.querySelector('.copy-btn')));
    });
  });
</script>
@endsection
