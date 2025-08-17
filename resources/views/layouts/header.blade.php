@php
    use Illuminate\Support\Facades\Auth;

    $user   = Auth::user();

    // Avatar fallback chain
    $avatar = $user?->profile_photo_url
            ?? $user?->avatar_url
            ?? asset('assets/images/user.png');

    // WhatsApp link (config/app.php or .env)
    $waPhone = config('app.support_whatsapp', env('SUPPORT_WHATSAPP', '+923124932021'));
    $waLink  = 'https://wa.me/' . preg_replace('/\D+/', '', $waPhone) . '?text=' . urlencode('Hello! I need help.');

    // YouTube link (config/app.php or .env)
    $ytLink  = config('app.youtube_url', env('YOUTUBE_URL', 'https://www.youtube.com/@YourChannel'));

    // Center logo path (use your actual file)
    $centerLogo = asset('asset/images/logo_92.png');  // 🔁 change if your path differs
@endphp

<div class="navbar-header position-relative">
  <div class="row align-items-center justify-content-between g-0">
    {{-- Left cluster: toggles --}}
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-4">
        <button type="button" class="sidebar-toggle">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
          <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
        </button>
        <button type="button" class="sidebar-mobile-toggle">
          <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
        </button>
      </div>
    </div>

    {{-- ⭐ Absolute centered logo (always visually centered) --}}
    <a href="{{ url('/') }}" class="header-center-logo">
      <img src="{{ $centerLogo }}" alt="Site logo" class="header-logo-img">
    </a>

    {{-- Right cluster: theme, WA, YouTube, profile --}}
    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">

        {{-- Theme toggle --}}
        <button type="button" data-theme-toggle
          class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

        {{-- WhatsApp --}}
        <a href="{{ $waLink }}" target="_blank" rel="noopener"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Chat on WhatsApp">
          <iconify-icon icon="ri:whatsapp-fill" class="text-success text-xl"></iconify-icon>
        </a>

        {{-- YouTube --}}
        <a href="{{ $ytLink }}" target="_blank" rel="noopener"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Visit our YouTube">
          <iconify-icon icon="ri:youtube-fill" class="text-danger text-xl"></iconify-icon>
        </a>

        {{-- Profile dropdown --}}
        <div class="dropdown">
          <button class="d-flex justify-content-center align-items-center rounded-circle" type="button"
                  data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $avatar }}" alt="{{ $user?->name ?? 'User' }}"
                 class="w-40-px h-40-px object-fit-cover rounded-circle">
          </button>

          <div class="dropdown-menu to-top dropdown-menu-sm">
            <div class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
              <div>
                <h6 class="text-lg text-primary-light fw-semibold mb-0">
                  {{ $user?->name ?? 'User' }}
                </h6>
              </div>
              <button type="button" class="hover-text-danger" data-bs-toggle="dropdown">
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
        </div><!-- /Profile dropdown -->

      </div>
    </div>
  </div>
</div>

<style>
  /* Header container: give it height and allow absolute children to be visible */
  .navbar-header{
    position: relative;
    min-height: 72px;       /* tweak to match your header */
    overflow: visible;      /* important so the centered logo is never clipped */
  }

  /* Absolutely center the logo (always visually centered) */
  .header-center-logo{
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    z-index: 101;           /* on top of header background */
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    pointer-events: none;   /* so it never steals clicks from header buttons */
  }

  .header-logo-img{
    height: 56px;           /* visible but not huge */
    width: auto;
    object-fit: contain;
    image-rendering: -webkit-optimize-contrast;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,.08));
  }

  /* Make sure the left/right controls always sit above the logo (and remain clickable) */
  .sidebar-toggle,
  .sidebar-mobile-toggle,
  .navbar-header .w-40-px,
  .navbar-header .dropdown{
    position: relative;
    z-index: 102;
  }

  /* Responsive sizing for the logo */
  @media (max-width: 991.98px){
    .header-logo-img{ height: 50px; }
  }
  @media (max-width: 575.98px){
    .header-logo-img{ height: 44px; }
  }

  /* Ultra-small safety fallback: if a very narrow device renders oddly,
     switch to static positioning so the logo still appears. */
  @media (max-width: 360px){
    .header-center-logo{
      position: static;
      transform: none;
      margin: 0 auto;
      pointer-events: none;
    }
    .navbar-header{
      display: flex;
      align-items: center;
      justify-content: center;
    }
  }

  /* Hide on anything below 768px */
@media (max-width: 767.98px){
  .header-center-logo { 
    display: none !important; 
  }
}

</style>

