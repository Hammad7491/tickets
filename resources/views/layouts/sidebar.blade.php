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
@endphp

<aside class="sidebar">
  <button type="button" class="sidebar-close-btn">
    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
  </button>

  <div>
    <a href="{{ $dashUrl }}" class="sidebar-logo">
      <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
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
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Admin List
              </a>
            </li>
            <li>
              <a href="{{ route('admin.users.list') }}"
                 class="{{ request()->routeIs('admin.users.list') ? 'is-active' : '' }}">
                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List
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

/* === Animated submenu ===
   We use max-height + padding transitions for smooth slide.
   No display toggling, so animation stays consistent.
*/
.sidebar-submenu{
  max-height:0;
  overflow:hidden;
  padding:0 10px; /* collapsed padding */
  transition:max-height .28s ease, padding .28s ease;
  will-change:max-height, padding;
}
.sidebar-submenu.show{
  max-height:500px; /* big enough for your largest submenu */
  padding:8px 10px 12px 10px; /* expanded padding */
}

/* Submenu links (theme preserved) */
.sidebar-submenu a {
  display:block; padding:10px 14px; border-radius:10px; color:#0f172a; font-weight:500; margin:6px 8px; text-decoration:none;
  transition:background-color .18s ease, color .18s ease;
}
.sidebar-submenu a:hover { background:#eaf1ff; }
.sidebar-submenu a.is-active { background:#eaf1ff; color:#1d4ed8; font-weight:600; }
.circle-icon { font-size:.55rem; margin-right:.35rem; }

/* Optional: nicer focus for keyboard users (keeps theme) */
.dropdown > a:focus-visible,
.sidebar-submenu a:focus-visible{
  outline:2px solid #4f7cff; outline-offset:2px; border-radius:10px;
}

/* Respect users who prefer reduced motion */
@media (prefers-reduced-motion: reduce){
  .dropdown .chev{ transition:none; }
  .sidebar-submenu{ transition:none; }
}

</style>

<script>
  (function () {
    const menu = document.getElementById('sidebar-menu');
    if (!menu) return;

    // Helper: toggle a dropdown's open state
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

    // Close all siblings of the given dropdown
    const closeSiblings = (current) => {
      menu.querySelectorAll(':scope > .dropdown').forEach(other => {
        if (other !== current) setOpen(other, false);
      });
    };

    // Ensure first click opens immediately and closes others
    menu.querySelectorAll(':scope > .dropdown > a').forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        // Prevent any default or bubbling that could cause a second click need
        e.preventDefault();
        e.stopPropagation();

        const li = trigger.parentElement;
        const isOpen = li.classList.contains('open');

        if (!isOpen) {
          closeSiblings(li);
          setOpen(li, true);   // open on first click
        } else {
          setOpen(li, false);  // close on second click
        }
      });

      // Also support Enter/Space for accessibility
      trigger.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          trigger.click();
        }
      });
    });

    // On load, normalize any server-rendered open states (keeps your route-based actives)
    menu.querySelectorAll(':scope > .dropdown').forEach(li => {
      const open = li.classList.contains('open');
      setOpen(li, open);
    });
  })();
</script>
