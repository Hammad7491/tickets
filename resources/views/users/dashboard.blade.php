@extends('layouts.app')

@section('content')
<div class="container pt-2 pb-5 page-gutter"><!-- keeps minor L/R space -->

  {{-- =================== HERO =================== --}}
  <section class="hero mb-12">
    <div class="hero-grid">
      {{-- IMAGE --}}
      <figure class="hero-visual" aria-label="92 Dream PK">
        <img
          src="{{ asset('asset/images/Home New.png') }}"
          alt="92 DREAM PK — Live Lottery"
          class="hero-img"
          loading="eager" decoding="async">
      </figure>

      {{-- COPY / ACTIONS --}}
      <div class="hero-copy">
        <h4 class="hero-title">Welcome, {{ $user->name }}</h4>
        <p class="hero-sub">92 DREAM PK • LIVE Lucky Draw</p>

        <div class="d-flex flex-wrap gap-2 mt-2">
          @if(Route::has('users.ticketstatus.index'))
            <a href="{{ route('users.ticketstatus.index') }}" class="btn btn-light btn-sm fw-semibold shadow-sm">My Tickets</a>
          @endif
          @if(Route::has('winners.index'))
            <a href="{{ route('winners.index') }}" class="btn btn-outline-light btn-sm fw-semibold shadow-sm">Winners</a>
          @endif
        </div>
      </div>
    </div>
  </section>
  {{-- ============================================ --}}

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
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
      @foreach($tickets as $t)
        @php
          $held = (int)($t->held_count ?? 0);
          $qty  = (int)$t->quantity;
          $remaining = max(0, $qty - $held);
        @endphp

        <div class="col">
          <div class="ticket-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
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
                    Buy Lucky Draw Ticket
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
  /* === page side padding === */
  .page-gutter{ padding-left: 13px !important; padding-right: 13px !important; }
  @media (min-width: 1200px){ .page-gutter{ padding-left: 16px !important; padding-right: 16px !important; } }

  /* keep hero off the edges */
  .hero{ margin-inline: 12px; }

  /* ======= DARK HERO (same look as screenshot) ======= */
  .hero{
    border-radius: 20px;
    background:
      radial-gradient(1200px 400px at 0% -10%, rgba(79,70,229,.35) 0%, transparent 55%),
      radial-gradient(1200px 400px at 120% 10%, rgba(6,182,212,.28) 0%, transparent 60%),
      linear-gradient(135deg, #111c3a 0%, #0f2d46 55%, #0b5360 100%); /* navy -> teal */
    color:#fff;
    box-shadow: 0 10px 24px rgba(2,8,23,.12);
    padding: clamp(16px, 3vw, 28px);
  }
  .hero-grid{
    display:grid;
    grid-template-columns: minmax(280px, 1.2fr) minmax(420px, 1fr);
    gap: clamp(16px, 3vw, 32px);
    align-items:center;
  }
  .hero-copy{ order:1; min-width:240px; }
  .hero-title{
    margin:0 0 .25rem 0;
    font-weight:800;
    letter-spacing:.2px;
    font-size: clamp(24px, 3vw, 40px);
    color:#fff;
  }
  .hero-sub{
    margin:0;
    color:rgba(255,255,255,.85);
    font-weight:600;
    font-size: clamp(12px, 1.6vw, 15px);
  }

  /* Visual: on dark hero we remove the white panel look */
  .hero-visual{
    order:2;
    margin:0;
    width: 100%;
    max-width: 680px;
    height: clamp(180px, 24vw, 260px);
    display:flex; align-items:center; justify-content:center;
    background: transparent;     /* no white box */
    border: 0;                   /* no border */
    border-radius:14px;
    box-shadow: none;            /* let image float cleanly */
    overflow:hidden;
  }
  .hero-img{
    width:100%; height:100%;
    object-fit:contain;
    image-rendering:-webkit-optimize-contrast;
    display:block;
    filter: drop-shadow(0 6px 16px rgba(0,0,0,.25)); /* subtle lift */
  }

  /* Buttons on dark hero: light & outline-light should stay readable */
  .hero .btn-light{
    background:#ffffff; border-color:#ffffff; color:#0f172a;
  }
  .hero .btn-light:hover{ background:#f8fafc; border-color:#f8fafc; color:#0f172a; }
  .hero .btn-outline-light{
    color:#ffffff; border-color:#ffffff; background:transparent;
  }
  .hero .btn-outline-light:hover{
    background:#ffffff; color:#0f172a; border-color:#ffffff;
  }

  /* Mobile */
  @media (max-width: 991.98px){
    .hero-grid{
      grid-template-columns: 1fr;
      text-align:center;
    }
    .hero-visual{
      order:-1; margin-inline:auto; max-width: 720px;
      height: clamp(180px, 48vw, 260px);
    }
  }

  /* ======= Tickets visuals (unchanged) ======= */
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
