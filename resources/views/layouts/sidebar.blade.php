@php
  use Illuminate\Support\Facades\Route;
  use Illuminate\Support\Facades\Auth;

  $authUser = Auth::user();

  // --- Role check (same)
  $isAdmin = Auth::check() && (
      (method_exists($authUser, 'hasRole') && $authUser->hasRole('admin')) ||
      (strtolower((string)($authUser->role ?? '')) === 'admin')
  );

  // --- Dashboard URL & active state (same)
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

  // --- Admin section actives (same)
  $isUsers   = request()->routeIs('admin.users.*');
  $isTickets = request()->routeIs('admin.tickets.*');

  // --- Admin: Ticket Review actives (same)
  $isReviewPending  = request()->routeIs('admin.reviews.pending');
  $isReviewAccepted = request()->routeIs('admin.reviews.accepted');
  $isTicketReview   = $isReviewPending || $isReviewAccepted;

  // --- User: "Ticket Status" (same)
  $statusRoute = Route::has('users.ticketstatus.index')
      ? 'users.ticketstatus.index'
      : (Route::has('users.dashboard') ? 'users.dashboard' : null);
  $statusUrl  = $statusRoute ? route($statusRoute) : url('/dashboard');
  $isStatus   = request()->routeIs('users.ticketstatus.*') || request()->routeIs('users.dashboard');

  // --- Winners: routes & actives (same)
  $hasAdminWinnersCreate = Route::has('admin.winners.create');
  $hasAdminWinnersIndex  = Route::has('admin.winners.index');
  $hasPublicWinnersIndex = Route::has('winners.index');

  $isWinnersAdmin  = request()->routeIs('admin.winners.*');
  $isWinnersPublic = request()->routeIs('winners.index');
  $isWinners       = $isWinnersAdmin || $isWinnersPublic;

  // --- Terms & Conditions: available to everyone
  $hasTerms  = Route::has('terms.show');
  $termsUrl  = $hasTerms ? route('terms.show') : url('/terms');
  $isTerms   = $hasTerms ? request()->routeIs('terms.show') : request()->is('terms');
@endphp

<aside class="sidebar">
  <button type="button" class="sidebar-close-btn">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>

  <div>
    <a href="{{ $dashUrl }}" class="sidebar-logo">
      <img src="{{ asset('asset/images/logo_92.png') }}" alt="site logo" class="light-logo">
      <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
      <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
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
            <li>
              {{-- <a href="{{ route('admin.users.list') }}"
                 class="{{ request()->routeIs('admin.users.list') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List
              </a> --}}
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

      {{-- WINNERS: Admin (Add+List) / Users (List only) --}}
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
            <span>Ticket Status</span>
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

      {{-- LEGAL: Terms & Conditions (everyone) --}}
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

<style>
/* === Sidebar (theme preserved) === */
.sidebar { width:270px; background:#fff; border-right:1px solid #eef0f4; }
.sidebar-menu { list-style:none; margin:0; padding:0; }
.sidebar-menu-group-title { padding:12px 18px; color:#6b7280; font-weight:600; font-size:.95rem; }

/* Parent row */
.dropdown > a {
  display:flex; align-items:center; gap:10px; padding:12px 16px; color:#0f172a; font-weight:600;
  border-radius:12px; margin:6px 10px; background:#f5f8ff; text-decoration:none; cursor:pointer;
  user-select:none; -webkit-tap-highlight-color: transparent;
}
.dropdown.open > a { background:#4f7cff; color:#fff; }
.dropdown .menu-icon { font-size:18px; }
.dropdown .chev { margin-left:auto; transition:transform .2s ease; }
.dropdown.open .chev { transform:rotate(180deg); }

/* Animated submenu */
.sidebar-submenu{
  max-height:0;
  overflow:hidden;
  padding:0 10px;
  transition:max-height .28s ease, padding .28s ease;
  will-change:max-height, padding;
}
.sidebar-submenu.show{
  max-height:500px;
  padding:8px 10px 12px 10px;
}

/* Submenu links */
.sidebar-submenu a {
  display:block; padding:10px 14px; border-radius:10px; color:#0f172a; font-weight:500; margin:6px 8px; text-decoration:none;
  transition:background-color .18s ease, color .18s ease;
}
.sidebar-submenu a:hover { background:#eaf1ff; }
.sidebar-submenu a.is-active { background:#eaf1ff; color:#1d4ed8; font-weight:600; }
.circle-icon { font-size:.55rem; margin-right:.35rem; }

/* Focus styles */
.dropdown > a:focus-visible,
.sidebar-submenu a:focus-visible{
  outline:2px solid #4f7cff; outline-offset:2px; border-radius:10px;
}

/* Reduced motion respect */
@media (prefers-reduced-motion: reduce){
  .dropdown .chev{ transition:none; }
  .sidebar-submenu{ transition:none; }
}

/* Bigger, crisp logo */
.light-logo{
  height: 60px !important;
  width: auto !important;
  object-fit: contain;
  image-rendering: -webkit-optimize-contrast;
  -ms-interpolation-mode: nearest-neighbor;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,.08));
}

/* Brand block centering */
.sidebar-logo{
  display:flex !important;
  align-items:center !important;
  justify-content:center !important;
  width:100%;
  height:84px;
  padding:0 !important;
  margin:0 auto !important;
  text-align:center !important;
}
.sidebar-logo .light-logo{
  height:64px;
  width:100px;
  object-fit:contain;
  image-rendering:-webkit-optimize-contrast;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,.08));
  display:block;
}
.sidebar-logo .dark-logo,
.sidebar-logo .logo-icon{
  display:none !important;
}

/* Responsive tweaks */
@media (min-width:1200px){
  .sidebar-logo{ height:92px; }
  .sidebar-logo .light-logo{ height:72px; }
}
@media (max-width:575.98px){
  .sidebar-logo{ height:74px; }
  .sidebar-logo .light-logo{ height:56px; }
}

.sidebar .dropdown > a::after{
  content: none !important;
}
</style>

<script>
  (function () {
    const menu = document.getElementById('sidebar-menu');
    if (!menu) return;

    const setOpen = (li, open) => {
      const sub = li.querySelector(':scope > .sidebar-submenu');
      if (open) {
        li.classList.add('open');
        sub && sub.classList.add('show');
      } else {
        li.classList.remove('open');
        sub && sub.classList.remove('show');
      }
    };

    const closeSiblings = (current) => {
      menu.querySelectorAll(':scope > .dropdown').forEach(other => {
        if (other !== current) setOpen(other, false);
      });
    };

    menu.querySelectorAll(':scope > .dropdown > a').forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const li = trigger.parentElement;
        const isOpen = li.classList.contains('open');

        if (!isOpen) {
          closeSiblings(li);
          setOpen(li, true);
        } else {
          setOpen(li, false);
        }
      });

      trigger.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          trigger.click();
        }
      });
    });

    // Normalize any server-rendered open states
    menu.querySelectorAll(':scope > .dropdown').forEach(li => {
      const open = li.classList.contains('open');
      setOpen(li, open);
    });
  })();
</script>
