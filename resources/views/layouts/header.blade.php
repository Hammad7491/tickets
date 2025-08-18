@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();

    // Avatar fallback chain (if you show it elsewhere)
    $avatar = $user?->profile_photo_url
            ?? $user?->avatar_url
            ?? asset('assets/images/user.png');

    // Support numbers / links (config or .env)
    $waPhoneRaw = config('app.support_whatsapp', env('SUPPORT_WHATSAPP', '+92 309 4040765')); // human-friendly
    $waPhoneDigits = preg_replace('/\D+/', '', $waPhoneRaw); // 923094040765 (no +, spaces, etc.)
    $waTextPlain = '92dreampk.com.'; // default message to prefill

    $ytLink = config('app.youtube_url', env('YOUTUBE_URL', 'https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a'));
    $centerLogo = asset('asset/images/logo_92.png');
@endphp

<div class="navbar-header position-relative">
  <div class="row align-items-center justify-content-between g-0">
    {{-- Left: sidebar toggles --}}
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
          <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
        </button>
        <button type="button" class="sidebar-mobile-toggle d-lg-none" aria-label="Toggle sidebar (mobile)">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
        </button>
      </div>
    </div>

    {{-- ⭐ Centered logo --}}
    <a href="{{ url('/') }}" class="header-center-logo" aria-label="Home">
      <img src="{{ $centerLogo }}" alt="Site logo" class="header-logo-img">
    </a>

    {{-- Right: WhatsApp, YouTube, Profile --}}
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">

        {{-- WhatsApp (prefilled) --}}
        <a href="#"
           id="waButton"
           data-wa-phone="{{ $waPhoneDigits }}"
           data-wa-text="{{ $waTextPlain }}"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Chat on WhatsApp" aria-label="WhatsApp">
          <iconify-icon icon="ri:whatsapp-fill" class="text-success text-xl"></iconify-icon>
        </a>

        {{-- YouTube --}}
        <a href="{{ $ytLink }}" target="_blank" rel="noopener"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Visit our YouTube" aria-label="YouTube">
          <iconify-icon icon="ri:youtube-fill" class="text-danger text-xl"></iconify-icon>
        </a>

        {{-- Profile dropdown --}}
        <div class="dropdown">
          <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                  data-bs-toggle="dropdown" aria-expanded="false" aria-label="Profile menu">
            <img src="{{ asset('asset/images/logo.png') }}" alt="{{ $user?->name ?? 'User' }}"
                 class="w-40-px h-40-px object-fit-cover rounded-circle">
          </button>

          <div class="dropdown-menu to-top dropdown-menu-sm">
            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">
                  {{ $user?->name ?? 'User' }}
                </h6>
              </div>
              <button type="button" class="hover-text-danger" data-bs-toggle="dropdown" aria-label="Close menu">
                <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
              </button>
            </div>

            <ul class="to-top-list">
              <li>
                <a href="{{ route('logout') }}"
                   class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                  Log Out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                  @csrf
                </form>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  /* ===== Header layout & centered logo ===== */
  .navbar-header{
    position: relative;
    min-height: 72px;
    overflow: visible;
  }
  .header-center-logo{
    position: absolute;
    left: 50%; top: 50%;
    transform: translate(-50%, -50%);
    z-index: 101;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    pointer-events: none; /* prevent stealing clicks */
  }
  .header-logo-img{
    height: 56px; width: auto; object-fit: contain;
    image-rendering: -webkit-optimize-contrast;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,.08));
  }
  .sidebar-toggle,
  .sidebar-mobile-toggle,
  .navbar-header .w-40-px,
  .navbar-header .dropdown{
    position: relative; z-index: 102;
  }
  @media (max-width: 991.98px){ .header-logo-img{ height: 50px; } }
  @media (max-width: 575.98px){ .header-logo-img{ height: 44px; } }

  /* Hide centered logo on small devices to keep room for actions */
  @media (max-width: 767.98px){
    .header-center-logo { display: none !important; }
  }

  /* ===== Icon swap when sidebar is open ===== */
  .sidebar-toggle .active{ display:none; }
  body.sidebar-open .sidebar-toggle .active{ display:inline-block; }
  body.sidebar-open .sidebar-toggle .non-active{ display:none; }

  /* ===== Off-canvas sidebar behavior (<=992px) ===== */
  @media (max-width: 991.98px){
    .sidebar{
      position: fixed; inset: 0 auto 0 0;
      width: 280px; max-width: 86%;
      height: 100vh; background: #fff;
      box-shadow: 0 10px 30px rgba(2,8,23,.2);
      transform: translateX(-100%);
      transition: transform .28s ease;
      z-index: 1031;
    }
    .sidebar.open{ transform: translateX(0); }

    .sidebar-backdrop{
      position: fixed; inset: 0;
      background: rgba(15,23,42,.45);
      opacity: 0; pointer-events: none;
      transition: opacity .2s ease;
      z-index: 1030;
    }
    .sidebar-backdrop.show{
      opacity: 1; pointer-events: auto;
    }
    body.no-scroll{ overflow: hidden; }
  }
</style>

<script>
  // === WhatsApp launcher with robust prefilled text ===
  (function () {
    const btn = document.getElementById('waButton');
    if (!btn) return;

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      const phone = (this.dataset.waPhone || '').trim();              // e.g., 923094040765
      const text  = encodeURIComponent(this.dataset.waText || '');    // encode now

      // Build variants
      const deepLink = `whatsapp://send?phone=${phone}&text=${text}`;               // App link
      const waMe     = `https://wa.me/${phone}?text=${text}`;                       // Universal
      const apiWeb   = `https://api.whatsapp.com/send?phone=${phone}&text=${text}`; // Most reliable on desktop

      const isDesktop = !/Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

      // Desktop → api.whatsapp.com (prefill works best)
      if (isDesktop) {
        window.open(apiWeb, '_blank');
        return;
      }

      // Mobile → try app deep link, then wa.me, then api fallback
      const start = Date.now();
      window.location.href = deepLink;

      setTimeout(() => {
        if (Date.now() - start < 1200) {
          window.location.href = waMe;

          setTimeout(() => {
            if (document.visibilityState === 'visible') {
              window.location.href = apiWeb;
            }
          }, 900);
        }
      }, 600);
    });
  })();

  // === Sidebar open/close handling (backdrop, Esc, resize) ===
  (function () {
    document.addEventListener('DOMContentLoaded', function () {
      const sidebar   = document.querySelector('.sidebar');
      const toggles   = document.querySelectorAll('.sidebar-toggle, .sidebar-mobile-toggle');

      // Backdrop (added once)
      const backdrop = document.createElement('div');
      backdrop.className = 'sidebar-backdrop';
      document.body.appendChild(backdrop);

      const open = () => {
        if (!sidebar) return;
        sidebar.classList.add('open');
        backdrop.classList.add('show');
        document.body.classList.add('no-scroll', 'sidebar-open');
      };
      const close = () => {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.classList.remove('no-scroll', 'sidebar-open');
      };
      const toggle = () => {
        if (!sidebar) return;
        sidebar.classList.contains('open') ? close() : open();
      };

      // Toggle on buttons
      toggles.forEach(btn => btn.addEventListener('click', function (e) {
        e.preventDefault();
        toggle();
      }));

      // Close on backdrop click & Esc
      backdrop.addEventListener('click', close);
      document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

      // Optional: if resizing to desktop, ensure closed
      let lastW = window.innerWidth;
      window.addEventListener('resize', function () {
        const w = window.innerWidth;
        if (lastW <= 991 && w > 991) close();
        lastW = w;
      });
    });
  })();
</script>
