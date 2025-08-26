@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;

    $authUser = Auth::user();

    // Role check
    $isAdmin =
        Auth::check() &&
        ((method_exists($authUser, 'hasRole') && $authUser->hasRole('admin')) ||
            strtolower((string) ($authUser->role ?? '')) === 'admin');

    // Dashboard URL
    $adminDashExists = Route::has('admin.dashboard');
    $userDashExists = Route::has('users.dashboard');

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
    $isUsers = request()->routeIs('admin.users.*');
    $isTickets = request()->routeIs('admin.tickets.*');

    // Ticket Review actives
    $isReviewPending = request()->routeIs('admin.reviews.pending');
    $isReviewAccepted = request()->routeIs('admin.reviews.accepted');
    $isTicketReview = $isReviewPending || $isReviewAccepted;

    // User: Ticket Status
    $statusRoute = Route::has('users.ticketstatus.index')
        ? 'users.ticketstatus.index'
        : (Route::has('users.dashboard')
            ? 'users.dashboard'
            : null);
    $statusUrl = $statusRoute ? route($statusRoute) : url('/dashboard');
    $isStatus = request()->routeIs('users.ticketstatus.*') || request()->routeIs('users.dashboard');

    // Winners
    $hasAdminWinnersCreate = Route::has('admin.winners.create');
    $hasAdminWinnersIndex = Route::has('admin.winners.index');
    $hasPublicWinnersIndex = Route::has('winners.index');

    $isWinnersAdmin = request()->routeIs('admin.winners.*');
    $isWinnersPublic = request()->routeIs('winners.index');
    $isWinners = $isWinnersAdmin || $isWinnersPublic;

    // Terms
    $hasTerms = Route::has('terms.show');
    $termsUrl = $hasTerms ? route('terms.show') : url('/terms');
    $isTerms = $hasTerms ? request()->routeIs('terms.show') : request()->is('terms');
@endphp

<!-- ========== SIDEBAR (must be a DIRECT CHILD of <body>) ========== -->
<aside id="app-sidebar" class="sidebar" aria-hidden="true">
    <button type="button" class="sidebar-close-btn" aria-label="Close sidebar" >
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
 

    <div>
        <a href="{{ $dashUrl }}" class="sidebar-logo">
            <img src="{{ asset('asset/images/LOGO LUCKY DRAW.png') }}" alt="site logo" class="light-logo">
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
            @if ($isAdmin)
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
                            <a href="{{ route('admin.users.index') }}"
                                class="{{ request()->routeIs('admin.users.show') ? 'is-active' : '' }}">
                                <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> User show
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- ADMIN-ONLY: Tickets --}}
            @if ($isAdmin)
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
            @if ($isAdmin || $hasPublicWinnersIndex)
                <li class="sidebar-menu-group-title">Winners</li>
                <li class="dropdown {{ $isWinners ? 'open' : '' }}">
                    <a href="javascript:void(0)">
                        <iconify-icon icon="mdi:trophy-outline" class="menu-icon"></iconify-icon>
                        <span>Winner Announce</span>
                        <iconify-icon icon="mdi:chevron-down" class="chev"></iconify-icon>
                    </a>
                    <ul class="sidebar-submenu {{ $isWinners ? 'show' : '' }}">
                        @if ($isAdmin)
                            @if ($hasAdminWinnersCreate)
                                <li>
                                    <a href="{{ route('admin.winners.create') }}"
                                        class="{{ request()->routeIs('admin.winners.create') ? 'is-active' : '' }}">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Add Winner
                                    </a>
                                </li>
                            @endif
                            @if ($hasAdminWinnersIndex)
                                <li>
                                    <a href="{{ route('admin.winners.index') }}"
                                        class="{{ request()->routeIs('admin.winners.index') ? 'is-active' : '' }}">
                                        <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Winners List
                                    </a>
                                </li>
                            @endif
                        @else
                            @if ($hasPublicWinnersIndex)
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
            @unless ($isAdmin)
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

<script>
document.querySelector(".sidebar-close-btn").addEventListener("click", function() {
  document.querySelector(".sidebar").classList.remove("sidebar-open");
  document.querySelector(".sidebar").classList.remove("open");
  document.querySelector(".sidebar-backdrop ").classList.remove("show");
document.querySelector("body").classList.remove("no-scroll", "sidebar-open");

  document.body.classList.remove("overlay-active");
});

</script>

<!-- Backdrop (must be a DIRECT CHILD of <body>) -->
