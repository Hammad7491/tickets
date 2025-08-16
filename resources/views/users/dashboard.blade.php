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
    {{-- Responsive grid: 1 / 2 / 3 / 4 columns --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
      @foreach($tickets as $t)
        @php
          // Controller should provide: withCount(['purchases as held_count' => fn($q)=>$q->whereIn('status',['pending','accepted'])])
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
                  <img
                    src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                    alt="{{ $t->name }}"
                    loading="lazy">
                @else
                  <div class="no-image">
                    <i class="bi bi-image text-muted fs-1"></i>
                    <div class="small text-muted">No Image</div>
                  </div>
                @endif
              </div>
            </div>

            <div class="p-3 d-flex flex-column gap-2">
              <div class="fw-semibold ticket-title">{{ $t->name }}</div>

              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                @if($remaining > 0)
                  <span class="badge bg-success-subtle text-success fw-bold order-1">
                    Remaining: {{ number_format($remaining) }}
                  </span>
                  <a href="{{ route('users.buy.create', $t->id) }}"
                     class="btn btn-primary btn-sm order-2 ms-auto">
                    Buy
                  </a>
                @else
                  <span class="badge bg-danger-subtle text-danger fw-bold order-1">Out of stock</span>
                  <button type="button" class="btn btn-secondary btn-sm order-2 ms-auto" disabled>Buy</button>
                @endif
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
  .ticket-serial{ display:none !important; }
  .ticket-card{ background:#fff; }
  .ticket-media{ position:relative; padding:12px; background:#f8f9fa; }
  .ticket-media-box{
    height:180px; border-radius:16px; overflow:hidden;
    box-shadow:inset 0 0 0 1px rgba(0,0,0,.06);
    background:#fff; display:flex; align-items:center; justify-content:center;
  }
  @media (max-width: 991.98px){ .ticket-media-box{ height:160px; } }
  @media (max-width: 575.98px){ .ticket-media{ padding:8px; } .ticket-media-box{ height:140px; border-radius:12px; } }
  .ticket-media-box img{ max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; object-position:center; display:block; }
  .no-image{
    height:100%; width:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.25rem;
    border:1px dashed #cbd5e1; border-radius:16px; background:#fff;
  }
  .ticket-title{ word-break: break-word; }
  .badge.bg-success-subtle{ background:#e9f9ef!important; color:#057a55!important; }
  .badge.bg-danger-subtle{  background:#fdebec!important; color:#b42318!important; }
  @media (max-width: 360px){
    .ticket-card .p-3{ padding:.75rem !important; }
    .ticket-card .btn{ padding:.375rem .625rem; }
  }
</style>
@endsection
