@php
    use Illuminate\Support\Facades\Route;

    // Try both names; fallback to "/"
    $dashCandidates = ['dashboard', 'admin.dashboard'];
    $dashName = collect($dashCandidates)->first(fn ($n) => Route::has($n));
    $dashUrl  = $dashName ? route($dashName) : url('/');
    $isDash   = $dashName ? request()->routeIs($dashName) : request()->is('/');

    $isUsers   = request()->routeIs('admin.users.*');
    $isTickets = request()->routeIs('admin.tickets.*');
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

      @can('view dashboard')
      <!-- Dashboard -->
      <li class="dropdown {{ $isDash ? 'open' : '' }}">
        <a href="javascript:void(0)">
          <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
          <span>Dashboard</span>
          <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
        </a>
        <ul class="sidebar-submenu {{ $isDash ? 'show' : '' }}">
          <li>
            <a href="{{ $dashUrl }}" class="{{ $isDash ? 'is-active' : '' }}">
              <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> AI
            </a>
          </li>
        </ul>
      </li>
      @endcan

      <!-- Users -->
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



            <li>
            <a href="{{ route('admin.users.list') }}"
               class="{{ request()->routeIs('admin.users.index') ? 'is-active' : '' }}">
              <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Users List
            </a>
          </li>
          </li>
        </ul>
      </li>

      <!-- Tickets -->
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

    </ul>
  </div>
</aside>

<style>
  /* Polished look */
  .sidebar { width:270px; background:#fff; border-right:1px solid #eef0f4; }
  .sidebar-menu { list-style:none; margin:0; padding:0; }
  .sidebar-menu-group-title { padding:12px 18px; color:#6b7280; font-weight:600; font-size:.95rem; }

  .dropdown > a {
    display:flex; align-items:center; gap:10px; padding:12px 16px; color:#0f172a; font-weight:600;
    border-radius:12px; margin:6px 10px; background:#f5f8ff;
  }
  .dropdown.open > a { background:#4f7cff; color:#fff; }
  .dropdown .menu-icon { font-size:18px; }
  .dropdown .chev { margin-left:auto; transition:transform .2s ease; }
  .dropdown.open .chev { transform:rotate(180deg); }

  .sidebar-submenu { display:none; padding:8px 10px 12px 10px; }
  .sidebar-submenu.show { display:block; }
  .sidebar-submenu a {
    display:block; padding:10px 14px; border-radius:10px; color:#0f172a; font-weight:500; margin:6px 8px;
  }
  .sidebar-submenu a:hover { background:#eaf1ff; }
  .sidebar-submenu a.is-active { background:#eaf1ff; color:#1d4ed8; font-weight:600; }

  .circle-icon { font-size:.55rem; margin-right:.35rem; }
</style>

<script>
  // Accordion: only one dropdown open at a time
  (function () {
    const menu = document.getElementById('sidebar-menu');
    if (!menu) return;

    menu.querySelectorAll(':scope > .dropdown > a').forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.preventDefault();
        const li  = trigger.parentElement;
        const sub = li.querySelector(':scope > .sidebar-submenu');

        // close others
        menu.querySelectorAll(':scope > .dropdown').forEach(other => {
          if (other !== li) {
            other.classList.remove('open');
            const osub = other.querySelector(':scope > .sidebar-submenu');
            osub && osub.classList.remove('show');
          }
        });

        // toggle current
        li.classList.toggle('open');
        sub && sub.classList.toggle('show');
      });
    });
  })();
</script>
