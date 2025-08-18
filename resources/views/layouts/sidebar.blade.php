@php
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Facades\Auth;

  $authUser = Auth::user();

  // Role check
  $isAdmin = Auth::check() && (
      (method_exists($authUser, 'hasRole') && $authUser->hasRole('admin')) ||
      (strtolower((string)($authUser->role ?? '')) === 'admin')
  );

  // Dashboard URL
  $adminDashExists = Route::has('admin.dashboard');
  $userDashExists  = Route::has('users.dashboard');

  if (!Auth::check()) {
      $dashUrl = route('login');
  } elseif ($isAdmin && $adminDashExists) {
      $dashUrl = route('admin.dashboard');
  } elseif (!$isAdmin && $userDashExists) {
      $dashUrl = route('users.dashboard');
  } else {
      $dashUrl = url('/');
  }

  $isDash = request()->routeIs('admin.dashboard') || request()->routeIs('users.dashboard');

  // Admin actives
  $isUsers   = request()->routeIs('admin.users.*');
  $isTickets = request()->routeIs('admin.tickets.*');

  // Ticket Review actives
  $isReviewPending  = request()->routeIs('admin.reviews.pending');
  $isReviewAccepted = request()->routeIs('admin.reviews.accepted');
  $isTicketReview   = $isReviewPending || $isReviewAccepted;

  // User: Ticket Status
  $statusRoute = Route::has('users.ticketstatus.index')
      ? 'users.ticketstatus.index'
      : (Route::has('users.dashboard') ? 'users.dashboard' : null);
  $statusUrl  = $statusRoute ? route($statusRoute) : url('/dashboard');
  $isStatus   = request()->routeIs('users.ticketstatus.*') || request()->routeIs('users.dashboard');

  // Winners
  $hasAdminWinnersCreate = Route::has('admin.winners.create');
  $hasAdminWinnersIndex  = Route::has('admin.winners.index');
  $hasPublicWinnersIndex = Route::has('winners.index');

  $isWinnersAdmin  = request()->routeIs('admin.winners.*');
  $isWinnersPublic = request()->routeIs('winners.index');
  $isWinners       = $isWinnersAdmin || $isWinnersPublic;

  // Terms
  $hasTerms  = Route::has('terms.show');
  $termsUrl  = $hasTerms ? route('terms.show') : url('/terms');
  $isTerms   = $hasTerms ? request()->routeIs('terms.show') : request()->is('terms');
@endphp

<!-- ========== SIDEBAR (must be a DIRECT CHILD of <body>) ========== -->
<aside id="app-sidebar" class="sidebar" aria-hidden="true">
  <button type="button"
          class="sidebar-close-btn"
          aria-label="Close sidebar"
          onclick="__hideSidebar()">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>

  <div>
    <a href="{{ $dashUrl }}" class="sidebar-logo">
      <img src="{{ asset('asset/images/logo_92.png') }}" alt="site logo" class="light-logo">
    </a>
  </div>

  <div class="sidebar-menu-area">
    <ul class="sidebar-menu" id="sidebar-menu">

      {{-- Dashboard (everyone) --}}
      <li class="dropdown {{ $isDash ? 'open' : '' }}">
        <a href="javascript:void(0)">
          <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
          <span>Dashboard</span>
          <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
        </a>
        <ul class="sidebar-submenu {{ $isDash ? 'show' : '' }}">
          <li>
            <a href="{{ $dashUrl }}" class="{{ $isDash ? 'is-active' : '' }}">
              <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
              {{ $isAdmin ? 'Admin Dashboard' : 'User Dashboard' }}
            </a>
          </li>
        </ul>
      </li>

      {{-- ADMIN-ONLY: Users --}}
      @if($isAdmin)
        <li class="sidebar-menu-group-title">Users</li>
        <li class="dropdown {{ $isUsers ? 'open' : '' }}">
          <a href="javascript:void(0)">
            <iconify-icon icon="mdi:account-multiple-outline" class="menu-icon"></iconify-icon>
            <span>User Management</span>
            <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
          </a>
          <ul class="sidebar-submenu {{ $isUsers ? 'show' : '' }}">
            <li>
              <a href="{{ route('admin.users.create') }}"
                 class="{{ request()->routeIs('admin.users.create') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Users
              </a>
            </li>
            <li>
              <a href="{{ route('admin.users.index') }}"
                 class="{{ request()->routeIs('admin.users.index') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> User List
              </a>
            </li>
          </ul>
        </li>
      @endif

      {{-- ADMIN-ONLY: Tickets --}}
      @if($isAdmin)
        <li class="sidebar-menu-group-title">Tickets</li>
        <li class="dropdown {{ $isTickets ? 'open' : '' }}">
          <a href="javascript:void(0)">
            <iconify-icon icon="solar:ticket-outline" class="menu-icon"></iconify-icon>
            <span>Ticket Management</span>
            <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
          </a>
          <ul class="sidebar-submenu {{ $isTickets ? 'show' : '' }}">
            <li>
              <a href="{{ route('admin.tickets.create') }}"
                 class="{{ request()->routeIs('admin.tickets.create') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Ticket
              </a>
            </li>
            <li>
              <a href="{{ route('admin.tickets.index') }}"
                 class="{{ request()->routeIs('admin.tickets.index') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Show Tickets
              </a>
            </li>
          </ul>
        </li>

        {{-- ADMIN-ONLY: Ticket Review --}}
        <li class="sidebar-menu-group-title">Ticket Review</li>
        <li class="dropdown {{ $isTicketReview ? 'open' : '' }}">
          <a href="javascript:void(0)">
            <iconify-icon icon="mdi:clipboard-check-outline" class="menu-icon"></iconify-icon>
            <span>Ticket Review</span>
            <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
          </a>
          <ul class="sidebar-submenu {{ $isTicketReview ? 'show' : '' }}">
            <li>
              <a href="{{ route('admin.reviews.pending') }}"
                 class="{{ $isReviewPending ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Pending Tickets
              </a>
            </li>
            <li>
              <a href="{{ route('admin.reviews.accepted') }}"
                 class="{{ $isReviewAccepted ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Accepted Tickets
              </a>
            </li>
          </ul>
        </li>
      @endif

      {{-- WINNERS --}}
      @if($isAdmin || $hasPublicWinnersIndex)
        <li class="sidebar-menu-group-title">Winners</li>
        <li class="dropdown {{ $isWinners ? 'open' : '' }}">
          <a href="javascript:void(0)">
            <iconify-icon icon="mdi:trophy-outline" class="menu-icon"></iconify-icon>
            <span>Winner Announce</span>
            <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
          </a>
          <ul class="sidebar-submenu {{ $isWinners ? 'show' : '' }}">
            @if($isAdmin)
              @if($hasAdminWinnersCreate)
                <li>
                  <a href="{{ route('admin.winners.create') }}"
                     class="{{ request()->routeIs('admin.winners.create') ? 'is-active' : '' }}">
                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Winner
                  </a>
                </li>
              @endif
              @if($hasAdminWinnersIndex)
                <li>
                  <a href="{{ route('admin.winners.index') }}"
                     class="{{ request()->routeIs('admin.winners.index') ? 'is-active' : '' }}">
                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Winners List
                  </a>
                </li>
              @endif
            @else
              @if($hasPublicWinnersIndex)
                <li>
                  <a href="{{ route('winners.index') }}"
                     class="{{ request()->routeIs('winners.index') ? 'is-active' : '' }}">
                    <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Winners List
                  </a>
                </li>
              @endif
            @endif
          </ul>
        </li>
      @endif

      {{-- USER-ONLY: Ticket Status --}}
      @unless($isAdmin)
        <li class="sidebar-menu-group-title">Check tickets</li>
        <li class="dropdown {{ $isStatus ? 'open' : '' }}">
          <a href="javascript:void(0)">
            <iconify-icon icon="mdi:clipboard-text-search-outline" class="menu-icon"></iconify-icon>
            <span>My Ticket</span>
            <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
          </a>
          <ul class="sidebar-submenu {{ $isStatus ? 'show' : '' }}">
            <li>
              <a href="{{ $statusUrl }}" class="{{ $isStatus ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Ticket Status
              </a>
            </li>
          </ul>
        </li>
      @endunless

      {{-- LEGAL --}}
      <li class="sidebar-menu-group-title">Legal</li>
      <li class="dropdown {{ $isTerms ? 'open' : '' }}">
        <a href="javascript:void(0)">
          <iconify-icon icon="mdi:file-document-outline" class="menu-icon"></iconify-icon>
          <span>Terms &amp; Conditions</span>
          <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
        </a>
        <ul class="sidebar-submenu {{ $isTerms ? 'show' : '' }}">
          <li>
            <a href="{{ $termsUrl }}" class="{{ $isTerms ? 'is-active' : '' }}">
              <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
              View Terms
            </a>
          </li>
        </ul>
      </li>

    </ul>
  </div>
</aside>

<!-- Backdrop (must be a DIRECT CHILD of <body>) -->
<div id="sidebar-backdrop" class="sidebar-backdrop" onclick="__hideSidebar()" aria-hidden="true"></div>

<style>
/* ===== General look (same as yours) ===== */
.sidebar { width:270px; background:#fff; border-right:1px solid #eef0f4; }
.sidebar-menu { list-style:none; margin:0; padding:0; }
.sidebar-menu-group-title { padding:12px 18px; color:#6b7280; font-weight:600; font-size:.95rem; }

.dropdown > a{ display:flex; align-items:center; gap:10px; padding:12px 16px; color:#0f172a; font-weight:600;
  border-radius:12px; margin:6px 10px; background:#f5f8ff; text-decoration:none; cursor:pointer; user-select:none; }
.dropdown.open > a{ background:#4f7cff; color:#fff; }
.dropdown .menu-icon{ font-size:18px; }
.dropdown .chev{ margin-left:auto; transition:transform .2s ease; }
.dropdown.open .chev{ transform:rotate(180deg); }

.sidebar-submenu{ max-height:0; overflow:hidden; padding:0 10px; transition:max-height .28s ease, padding .28s ease; }
.sidebar-submenu.show{ max-height:500px; padding:8px 10px 12px 10px; }
.sidebar-submenu a{ display:block; padding:10px 14px; border-radius:10px; color:#0f172a; font-weight:500; margin:6px 8px; text-decoration:none; }
.sidebar-submenu a:hover{ background:#eaf1ff; }
.sidebar-submenu a.is-active{ background:#eaf1ff; color:#1d4ed8; font-weight:600; }
.circle-icon{ font-size:.55rem; margin-right:.35rem; }

.sidebar-logo{ display:flex; align-items:center; justify-content:center; width:100%; height:84px; }
.sidebar-logo .light-logo{ height:64px; width:100px; object-fit:contain; image-rendering:-webkit-optimize-contrast; filter: drop-shadow(0 2px 4px rgba(0,0,0,.08)); }

/* ===== iOS/Safari FIXES ===== */
html, body { height:100%; }
body { -webkit-overflow-scrolling: touch; }

/* Highest z-index so nothing covers it */
.sidebar { z-index: 2147483000; }
.sidebar-backdrop { z-index: 2147482000; }

/* Neutralize transforms/filters on wrappers when sidebar is open */
body.sidebar-open .page-wrapper,
body.sidebar-open .app,
body.sidebar-open .layout,
body.sidebar-open header,
body.sidebar-open main {
  transform: none !important;
  filter: none !important;
  perspective: none !important;
  contain: none !important;
}

/* ===== Off–canvas behavior (translate3d for iOS) ===== */
@media (max-width: 991.98px){
  body { overflow-x: hidden; }

  /* Sidebar & backdrop must be DIRECT children of body */
  body > .sidebar{
    position: fixed; top:0; left:0; right:auto; bottom:0;
    width:270px; background:#fff;
    box-shadow: 0 10px 30px rgba(15,23,42,.15);
    transform: translate3d(-100%,0,0);          /* hidden by default */
    transition: transform .28s ease;
    will-change: transform;
    contain: layout paint size style;
  }
  body.sidebar-open > .sidebar,
  body > .sidebar.is-open{
    transform: translate3d(0,0,0);
  }

  /* Close button visible & tappable */
  .sidebar .sidebar-close-btn{
    position:absolute; top:10px; right:10px;
    display:inline-flex; align-items:center; justify-content:center;
    width:36px; height:36px; border:0; background:#edf2ff; border-radius:10px; cursor:pointer;
    z-index: 2147483500; /* above everything */
  }

  /* Backdrop */
  body > .sidebar-backdrop{
    position: fixed; inset:0; background: rgba(15,23,42,.45);
    opacity: 0; pointer-events:none; transition: opacity .2s ease;
  }
  body.sidebar-open > .sidebar-backdrop,
  body > .sidebar.is-open ~ .sidebar-backdrop{
    opacity: 1; pointer-events: auto;
  }
}

/* Prevent body scroll when open */
body.sidebar-open{ overflow:hidden; }
</style>

<script>
(function(){
  const menu = document.getElementById('sidebar-menu');
  const sidebar = document.getElementById('app-sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');

  // Dropdowns
  if (menu){
    menu.addEventListener('click', function(e){
      const a = e.target.closest('.dropdown > a');
      if (!a) return;
      e.preventDefault();
      const li = a.parentElement;
      const open = li.classList.contains('open');
      Array.from(menu.children).forEach(s=>{
        if(s!==li && s.classList && s.classList.contains('dropdown')){
          s.classList.remove('open');
          s.querySelector('.sidebar-submenu')?.classList.remove('show');
        }
      });
      li.classList.toggle('open', !open);
      li.querySelector('.sidebar-submenu')?.classList.toggle('show', !open);
    });
  }

  // Helpers
  const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

  window.__showSidebar = function(){
    if (!sidebar) return;
    if (isMobile()) {
      document.body.classList.add('sidebar-open');
      sidebar.classList.add('is-open');
      sidebar.setAttribute('aria-hidden','false');
    }
  };

  window.__hideSidebar = function(){
    if (!sidebar) return;
    document.body.classList.remove('sidebar-open');
    sidebar.classList.remove('is-open');
    sidebar.setAttribute('aria-hidden','true');
  };

  // Backdrop & ESC
  backdrop?.addEventListener('click', __hideSidebar);
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') __hideSidebar(); });

  // Initial state
  function init(){
    if (isMobile()){
      sidebar.setAttribute('aria-hidden','true');
    } else {
      // desktop: keep visible; off-canvas only applies on mobile
      sidebar.setAttribute('aria-hidden','false');
    }
  }
  init();
  window.addEventListener('resize', init);
})();
</script>

<!-- Example hamburger (place in your header, mobile-only) -->
<!-- <button type="button" class="d-lg-none" onclick="__showSidebar()">
  <iconify-icon icon="mdi:menu"></iconify-icon>
</button> -->
