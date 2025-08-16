@php
    use Illuminate\Support\Facades\Auth;

    $user   = Auth::user();

    // Avatar fallback chain
    $avatar = $user?->profile_photo_url
            ?? $user?->avatar_url
            ?? asset('assets/images/user.png');

    // WhatsApp link (config/app.php or .env)
    // In config/app.php: 'support_whatsapp' => '+923001234567'
    // Or .env: SUPPORT_WHATSAPP=+923001234567
    $waPhone = config('app.support_whatsapp', env('SUPPORT_WHATSAPP', '+923124932021'));
    $waLink  = 'https://wa.me/' . preg_replace('/\D+/', '', $waPhone) . '?text=' . urlencode('Hello! I need help.');

    // YouTube link (config/app.php or .env)
    // In config/app.php: 'youtube_url' => 'https://www.youtube.com/@YourChannel'
    // Or .env: YOUTUBE_URL=https://www.youtube.com/@YourChannel
    $ytLink  = config('app.youtube_url', env('YOUTUBE_URL', 'https://www.youtube.com/@YourChannel'));
@endphp

<div class="navbar-header">
  <div class="row align-items-center justify-content-between">
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

    <div class="col-auto">
      <div class="d-flex flex-wrap align-items-center gap-3">

        {{-- Theme toggle (kept) --}}
        <button type="button" data-theme-toggle
          class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

        {{-- WhatsApp (new) --}}
        <a href="{{ $waLink }}" target="_blank" rel="noopener"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Chat on WhatsApp">
          <iconify-icon icon="ri:whatsapp-fill" class="text-success text-xl"></iconify-icon>
        </a>

        {{-- YouTube (new) --}}
        <a href="{{ $ytLink }}" target="_blank" rel="noopener"
           class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"
           title="Visit our YouTube">
          <iconify-icon icon="ri:youtube-fill" class="text-danger text-xl"></iconify-icon>
        </a>

        {{-- Profile dropdown (avatar button → name + logout only) --}}
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
              {{-- Only Logout remains --}}
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
        </div><!-- Profile dropdown end -->

      </div>
    </div>
  </div>
</div>
