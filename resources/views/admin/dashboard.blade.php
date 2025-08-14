{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')
@section('content')

<div class="dashboard-main-body container-fluid px-3 px-sm-4 px-lg-5">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 gap-sm-3 mb-16 mb-sm-24">
    <h6 class="fw-semibold mb-0" style="font-size:clamp(14px, 2vw, 18px)">Dashboard</h6> 
    <ul class="d-flex align-items-center gap-2 flex-wrap text-truncate" style="max-width:100%">
      <li class="fw-medium d-flex align-items-center gap-1 text-truncate">
        <iconify-icon icon="solar:home-smile-angle-outline" class="icon" style="font-size:clamp(16px,2.6vw,20px)"></iconify-icon>
        <span class="text-truncate">Dashboard</span>
      </li>
      <li class="d-none d-sm-inline">-</li>
      <li class="fw-medium d-none d-sm-inline">AI</li>
    </ul>
  </div>

  {{-- KPI ROW (3 cards) --}}
  <div class="row gy-3 gx-3 gx-md-4">
    {{-- Total Users --}}
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card shadow-none border bg-gradient-start-1 h-100">
        <div class="card-body p-16 p-md-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1" style="font-size:clamp(12px,1.6vw,14px)">Total Users</p>
              <h6 class="mb-0" style="font-size:clamp(18px,3vw,22px)">{{ number_format($totalUsers) }}</h6>
            </div>
            <div class="rounded-circle d-flex justify-content-center align-items-center"
                 style="width:48px;height:48px;background:#06b6d4">
              <iconify-icon icon="gridicons:multiple-users" class="text-white" style="font-size:clamp(18px,3.2vw,24px)"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Users registered</p>
        </div>
      </div>
    </div>

    {{-- Logged-in Users --}}
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card shadow-none border bg-gradient-start-2 h-100">
        <div class="card-body p-16 p-md-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1" style="font-size:clamp(12px,1.6vw,14px)">Logged-in Users (Now)</p>
              <h6 class="mb-0" style="font-size:clamp(18px,3vw,22px)">{{ number_format($onlineUsers) }}</h6>
            </div>
            <div class="rounded-circle d-flex justify-content-center align-items-center"
                 style="width:48px;height:48px;background:#7c3aed">
              <iconify-icon icon="mdi:account-check" class="text-white" style="font-size:clamp(18px,3.2vw,24px)"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Active sessions</p>
        </div>
      </div>
    </div>

    {{-- Total Tickets --}}
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card shadow-none border bg-gradient-start-3 h-100">
        <div class="card-body p-16 p-md-20">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <p class="fw-medium text-primary-light mb-1" style="font-size:clamp(12px,1.6vw,14px)">Total Tickets</p>
              <h6 class="mb-0" style="font-size:clamp(18px,3vw,22px)">{{ number_format($totalTickets) }}</h6>
            </div>
            <div class="rounded-circle d-flex justify-content-center align-items-center"
                 style="width:48px;height:48px;background:#0ea5e9">
              <iconify-icon icon="solar:ticket-outline" class="text-white" style="font-size:clamp(18px,3.2vw,24px)"></iconify-icon>
            </div>
          </div>
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">4-digit unique codes</p>
        </div>
      </div>
    </div>
  </div>

  {{-- TICKETS (SERIAL + IMAGE) --}}
  <div class="card h-100 mt-4">
    <div class="card-body p-16 p-md-24">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="mb-0 fw-bold" style="font-size:clamp(16px,2.6vw,20px)">Tickets</h6>

        <div class="d-flex align-items-center gap-2 ms-auto w-100 w-sm-auto">
          <div class="input-group input-group-sm" style="max-width:320px; width:100%;">
            <span class="input-group-text">
              <iconify-icon icon="solar:magnifer-linear" class="text-muted"></iconify-icon>
            </span>
            <input id="ticketSearch" type="text" class="form-control"
                   placeholder="Type serial (e.g., PK123456)" autocomplete="off" inputmode="latin-prose">
            <button class="btn btn-outline-secondary" type="button" id="clearTicketSearch" style="display:none;">
              Clear
            </button>
          </div>
          <span id="ticketsCount" class="text-secondary-light text-sm ms-sm-2">
            {{ isset($tickets) ? $tickets->count() : 0 }} shown
          </span>
        </div>
      </div>

      @if(isset($tickets) && $tickets->count())
        {{-- CSS Grid for fully fluid responsive columns --}}
        <div id="ticketsGrid" class="tickets-grid mt-3">
          @foreach($tickets as $t)
            <div class="ticket-col">
              <button type="button" class="ticket-card w-100 text-start" title="Tap to copy" data-serial="{{ $t->serial }}">
                <div class="ticket-serial">{{ $t->serial }}</div>

                @if($t->image_path)
                  <img
                    src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                    alt="Ticket Image for {{ $t->serial }}"
                    class="ticket-img"
                    loading="lazy"
                    decoding="async"
                  >
                @else
                  <div class="ticket-placeholder">No Image</div>
                @endif
              </button>
            </div>
          @endforeach
        </div>
        <div id="noTicketMatch" class="text-secondary-light mt-3" style="display:none;">No matching serials.</div>
      @else
        <p class="text-secondary-light mt-3 mb-0">No tickets found.</p>
      @endif
    </div>
  </div>
</div>

{{-- TICKET CARD STYLES --}}
<style>
/* ===== Tickets layout: left-aligned, wider cards ===== */
.tickets-grid{
  display:flex;
  flex-wrap:wrap;
  gap:14px;
  justify-content:flex-start;   /* ⬅ left align */
  align-items:flex-start;
}

.ticket-col{
  flex:0 0 auto;
  width: 320px;                 /* base width so image fits fully */
}
@media (min-width:576px){ .ticket-col{ width:360px; } }
@media (min-width:768px){ .ticket-col{ width:420px; } }
@media (min-width:1200px){ .ticket-col{ width:520px; } }

/* ===== Ticket card (compact but wide) ===== */
.ticket-card{
  appearance:none;
  border:none;
  background:#fff;
  position:relative;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:flex-start;
  padding:10px;
  border:1px solid #e5e7eb;
  border-radius:14px;
  cursor:pointer;
  box-shadow:0 2px 8px rgba(15,23,42,.06);
  transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
  overflow:hidden;
  min-height:110px;             /* short, since image height is capped */
  touch-action:manipulation;
}
@media (hover:hover){
  .ticket-card:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(15,23,42,.10);
    border-color:#c7d2fe; background:#f8fafc;
  }
}

/* Side notches (kept smaller) */
.ticket-card::before,
.ticket-card::after{
  content:"";
  position:absolute; top:50%; transform:translateY(-50%);
  width:16px; height:16px;
  border-radius:50%;
  background: var(--dash-bg, #f5f7fb);
  border:1px solid #e5e7eb;
  z-index:1;
}
.ticket-card::before{ left:-8px; }
.ticket-card::after{ right:-8px; }

.ticket-serial{
  font-weight:800;
  letter-spacing:.4px;
  font-size:14px;
  color:#111827;
  margin-bottom:6px;
  width:100%;
  text-align:center;
  line-height:1.1;
}

/* ===== Image: show full ticket (no crop), scale to width ===== */
.ticket-img{
  width:100%;
  max-height:120px;            /* increase so full ticket is visible */
  height:auto;                 /* keep aspect ratio */
  object-fit:contain;          /* no cropping */
  background:#fff;             /* avoid gray around contain */
  border-radius:10px;
  border:1px solid #e5e7eb;
  display:block;
  -webkit-user-drag:none;
}
.ticket-placeholder{
  width:100%;
  height:120px;
  display:flex; align-items:center; justify-content:center;
  color:#6b7280;
  border:1px dashed #cfd4dc;
  border-radius:10px;
  font-size:13px;
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce){
  .ticket-card{ transition:none; }
}

/* Tighten vertical rhythm on very small screens */
@media (max-width:375px){
  .card-body{ padding:12px !important; }
}

</style>


{{-- Copy + Realtime filter + notch bg sync (mobile-friendly) --}}
<script>
  // Copy serial to clipboard (with graceful fallback)
  document.querySelectorAll('.ticket-card').forEach(card=>{
    card.addEventListener('click', async ()=>{
      const serial = card.dataset.serial || '';
      if (!serial) return;
      try {
        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(serial);
        } else {
          const ta = document.createElement('textarea');
          ta.value = serial; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
        }
      } catch(e) {}
      card.classList.add('copied');
      const label = card.querySelector('.ticket-serial');
      const old = label?.textContent || '';
      if (label) label.textContent = serial + ' ✓';
      setTimeout(()=>{ card.classList.remove('copied'); if (label) label.textContent = old; }, 900);
    }, { passive:true });
  });

  // Sync notch fill with page background
  (function(){
    const bg = getComputedStyle(document.body).backgroundColor || '#f5f7fb';
    document.documentElement.style.setProperty('--dash-bg', bg);
  })();

  // Real-time filter by serial (debounced)
  (function(){
    const input  = document.getElementById('ticketSearch');
    const clear  = document.getElementById('clearTicketSearch');
    const count  = document.getElementById('ticketsCount');
    const grid   = document.getElementById('ticketsGrid');
    const noMatch= document.getElementById('noTicketMatch');

    if(!grid) return;
    const cols   = Array.from(grid.querySelectorAll('.ticket-col'));

    function applyFilter(q){
      q = (q || '').trim().toLowerCase();
      let shown = 0;
      cols.forEach(col=>{
        const card = col.querySelector('.ticket-card');
        const serial = (card?.dataset.serial || '').toLowerCase();
        const match = !q || serial.includes(q);
        col.style.display = match ? '' : 'none';
        if (match) shown++;
      });
      if (count) count.textContent = `${shown} shown`;
      if (clear) clear.style.display = q ? '' : 'none';
      if (noMatch) noMatch.style.display = shown ? 'none' : '';
    }

    let t; const deb = fn => (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), 120); };
    input?.addEventListener('input', deb(()=>applyFilter(input.value)));
    clear?.addEventListener('click', ()=>{ input.value=''; applyFilter(''); input.focus(); });

    applyFilter('');
  })();
</script>

@endsection
