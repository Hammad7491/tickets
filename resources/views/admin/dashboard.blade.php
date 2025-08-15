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
          <p class="fw-medium text-sm text-primary-light mt-12 mb-0">Ticket items</p>
        </div>
      </div>
    </div>
  </div>

  {{-- TICKETS --}}
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
                   placeholder="Type ticket name" autocomplete="off" inputmode="latin-prose">
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
        <div class="tickets-wrap">
          <div id="ticketsGrid" class="tickets-grid mt-3">
            @foreach($tickets as $t)
              @php
                $held = (int) ($t->held_count ?? 0);      // pending + accepted
                $remaining = max(0, (int)$t->quantity - $held);
              @endphp
              <div class="ticket-col">
                <div class="ticket-card w-100">
                  <div class="ticket-name" title="{{ $t->name }}">{{ $t->name }}</div>

                  @if($t->image_path)
                    <img
                      src="{{ route('admin.tickets.image', ['path' => $t->image_path]) }}"
                      alt="Ticket Image for {{ $t->name }}"
                      class="ticket-img"
                      loading="lazy"
                      decoding="async"
                    >
                  @else
                    <div class="ticket-placeholder">No Image</div>
                  @endif

                  <div class="d-flex justify-content-end align-items-center w-100 mt-2">
                    @if($remaining > 0)
                      <span class="badge remaining-badge">
                        Remaining: <strong>{{ number_format($remaining) }}</strong>
                      </span>
                    @else
                      <span class="badge out-badge"><strong>Out of stock</strong></span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div id="noTicketMatch" class="text-secondary-light mt-3" style="display:none;">No matching names.</div>
        </div>
      @else
        <p class="text-secondary-light mt-3 mb-0">No tickets found.</p>
      @endif
    </div>
  </div>
</div>

{{-- STYLES --}}
<style>
/* ✅ Keep compact width but LEFT align the block inside the card */
.tickets-wrap{
  max-width: 740px;
  margin: 0;                   /* was auto; ensures left alignment */
}

/* ✅ Left-align the items in the row */
.tickets-grid{
  display:flex;
  flex-wrap:wrap;
  gap:14px;
  justify-content:flex-start;  /* was center */
  align-items:flex-start;
}

/* Slimmer card width across breakpoints */
.ticket-col{ flex:0 0 auto; width: 280px; }
@media (min-width:576px){ .ticket-col{ width:300px; } }
@media (min-width:768px){ .ticket-col{ width:340px; } }
@media (min-width:1200px){ .ticket-col{ width:360px; } }

/* Card visuals */
.ticket-card{
  background:#fff;
  position:relative;
  display:flex;
  flex-direction:column;
  align-items:center;
  padding:12px;
  border:1px solid #e5e7eb;
  border-radius:14px;
  box-shadow:0 2px 8px rgba(15,23,42,.06);
}

.ticket-name{
  font-weight:800;
  letter-spacing:.3px;
  font-size:clamp(14px, 2.4vw, 16px);
  color:#111827;
  margin-bottom:6px;
  width:100%;
  text-align:center;
  line-height:1.2;
  word-break:break-word;
}

/* Image */
.ticket-img{
  width:100%;
  max-height:120px;
  height:auto;
  object-fit:contain;
  background:#fff;
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

/* Badges */
.badge.remaining-badge{ background:#e9f9ef !important; color:#057a55; font-weight:700; }
.badge.out-badge{ background:#fdebec !important; color:#b42318; font-weight:800; }

/* Misc */
@media (prefers-reduced-motion: reduce){ .ticket-card{ transition:none; } }
@media (max-width:375px){ .card-body{ padding:12px !important; } }
</style>

{{-- JS: filter by name --}}
<script>
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
        const nameEl = col.querySelector('.ticket-name');
        const name = (nameEl?.textContent || '').toLowerCase();
        const match = !q || name.includes(q);
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
