@extends('layouts.app')
@section('content')

<div class="dashboard-main-body">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
    <h6 class="fw-semibold mb-0">Dashboard</h6>
    <ul class="d-flex align-items-center gap-2">
      <li class="fw-medium d-flex align-items-center gap-1">
        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
        Dashboard
      </li>
      <li>-</li>
      <li class="fw-medium">AI</li>
    </ul>
  </div>

  {{-- KPI ROW (3 cards only) --}}
  <div class="row row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
    {{-- Total Users --}}
    <div class="col">
      <div class="card shadow-none border bg-gradient-start-1 h-100">
        <div class="card-body p-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1">Total Users</p>
              <h6 class="mb-0">{{ number_format($totalUsers) }}</h6>
            </div>
            <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
              <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Users registered</p>
        </div>
      </div>
    </div>

    {{-- Logged-in Users --}}
    <div class="col">
      <div class="card shadow-none border bg-gradient-start-2 h-100">
        <div class="card-body p-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1">Logged-in Users (Now)</p>
              <h6 class="mb-0">{{ number_format($onlineUsers) }}</h6>
            </div>
            <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
              <iconify-icon icon="mdi:account-check" class="text-white text-2xl mb-0"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Active sessions</p>
        </div>
      </div>
    </div>

    {{-- Total Tickets --}}
    <div class="col">
      <div class="card shadow-none border bg-gradient-start-3 h-100">
        <div class="card-body p-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1">Total Tickets</p>
              <h6 class="mb-0">{{ number_format($totalTickets) }}</h6>
            </div>
            <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
              <iconify-icon icon="solar:ticket-outline" class="text-white text-2xl mb-0"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">4-digit unique codes</p>
        </div>
      </div>
    </div>
  </div>

  {{-- TICKETS (CODES) --}}
  <div class="card h-100 mt-4">
    <div class="card-body p-24">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold text-lg">Tickets (Codes)</h6>

        <div class="d-flex align-items-center gap-2 ms-auto">
          <div class="input-group input-group-sm" style="max-width: 240px;">
            <span class="input-group-text">
              <iconify-icon icon="solar:magnifer-linear" class="text-muted"></iconify-icon>
            </span>
            <input id="ticketSearch" type="text" class="form-control"
                   placeholder="Filter by code (e.g., 1536)" autocomplete="off">
            <button class="btn btn-outline-secondary" type="button" id="clearTicketSearch" style="display:none;">
              Clear
            </button>
          </div>
          <span id="ticketsCount" class="text-secondary-light text-sm">
            {{ isset($tickets) ? $tickets->count() : 0 }} shown
          </span>
        </div>
      </div>

      @if(isset($tickets) && $tickets->count())
        <div id="ticketsGrid" class="row row-cols-xxl-6 row-cols-lg-5 row-cols-md-4 row-cols-3 row-cols-2 gy-3 gx-3 mt-3">
          @foreach($tickets as $t)
            <div class="col ticket-col">
              <button type="button" class="ticket-card w-100" data-code="{{ $t->code }}" title="Click to copy">
                <span class="ticket-code">{{ $t->code }}</span>
                <span class="ticket-perf"></span>
              </button>
            </div>
          @endforeach
        </div>
        <div id="noTicketMatch" class="text-secondary-light mt-3" style="display:none;">No matching codes.</div>
      @else
        <p class="text-secondary-light mt-3 mb-0">No tickets found.</p>
      @endif
    </div>
  </div>
</div>

{{-- TICKET CARD STYLES --}}
<style>
  .ticket-card{
    position:relative;
    display:flex; align-items:center; justify-content:center;
    height:78px; padding:0 28px;
    border:1px solid #e5e7eb; border-radius:16px;
    background:#ffffff; cursor:pointer;
    box-shadow:0 2px 8px rgba(15,23,42,.06);
    transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
    overflow:hidden;
  }
  .ticket-card:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(15,23,42,.10);
    border-color:#c7d2fe;
    background:#f8fafc;
  }
  .ticket-card::before,
  .ticket-card::after{
    content:"";
    position:absolute; top:50%; transform:translateY(-50%);
    width:22px; height:22px; border-radius:50%;
    background: var(--dash-bg, #f5f7fb);
    border:1px solid #e5e7eb;
    z-index:2;
  }
  .ticket-card::before{ left:-11px; }
  .ticket-card::after{ right:-11px; }
  .ticket-perf{
    position:absolute; top:10px; bottom:10px; left:50%; width:2px; transform:translateX(-50%);
    background-image: repeating-linear-gradient(to bottom,#d1d5db 0,#d1d5db 6px,transparent 6px,transparent 12px);
    opacity:.9;
  }
  .ticket-code{ font-weight:800; letter-spacing:3px; font-size:22px; color:#111827; }
  .ticket-card.copied{ background:#dcfce7; border-color:#86efac; }
  @media (max-width: 576px){
    .ticket-card{ height:70px; }
    .ticket-code{ font-size:20px; letter-spacing:2px; }
  }
</style>

{{-- Copy + Filter --}}
<script>
  // Copy ticket code to clipboard
  document.querySelectorAll('.ticket-card').forEach(el=>{
    el.addEventListener('click', ()=>{
      const code = el.dataset.code || el.textContent.trim();
      try { navigator.clipboard?.writeText(code); } catch(e) {}
      el.classList.add('copied');
      const span = el.querySelector('.ticket-code');
      const old = span.textContent;
      span.textContent = old + ' ✓';
      setTimeout(()=>{ el.classList.remove('copied'); span.textContent = old; }, 900);
    });
  });

  // Page background -> notch fill
  (function(){
    const bg = getComputedStyle(document.body).backgroundColor || '#f5f7fb';
    document.documentElement.style.setProperty('--dash-bg', bg);
  })();

  // Filter by code (client-side)
  (function(){
    const input = document.getElementById('ticketSearch');
    const clear = document.getElementById('clearTicketSearch');
    const count = document.getElementById('ticketsCount');
    const noMatch = document.getElementById('noTicketMatch');
    const cards = Array.from(document.querySelectorAll('.ticket-card'));

    function applyFilter(){
      const q = (input.value || '').trim().toLowerCase();
      let shown = 0;

      cards.forEach(card=>{
        const code = (card.dataset.code || '').toLowerCase();
        const match = code.includes(q);
        const col = card.closest('.ticket-col') || card.parentElement;
        col.style.display = match ? '' : 'none';
        if (match) shown++;
      });

      count.textContent = `${shown} shown`;
      clear.style.display = q ? '' : 'none';
      if (noMatch) noMatch.style.display = shown ? 'none' : '';
    }

    function debounce(fn, ms){ let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args), ms); }; }

    input?.addEventListener('input', debounce(applyFilter, 120));
    clear?.addEventListener('click', ()=>{ input.value=''; applyFilter(); input.focus(); });

    // initial
    applyFilter();
  })();
</script>

@endsection
