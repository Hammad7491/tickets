{{-- resources/views/welcome.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  $logo       = asset('asset/images/LOGO LUCKY DRAW.png');
  $heroImg    = asset('asset/images/landing/hero.jpg');

  // “How it works” icons (fallback to site logo)
  $icoCreate  = file_exists(public_path('asset/images/landing/icons/create.png')) ? asset('asset/images/landing/icons/create.png') : $logo;
  $icoBuy     = file_exists(public_path('asset/images/landing/icons/buy.png'))    ? asset('asset/images/landing/icons/buy.png')    : $logo;
  $icoWatch   = file_exists(public_path('asset/images/landing/icons/watch.png'))  ? asset('asset/images/landing/icons/watch.png')  : $logo;
  $icoPaid    = file_exists(public_path('asset/images/landing/icons/paid.png'))   ? asset('asset/images/landing/icons/paid.png')   : $logo;

  $youtubeUrl = config('app.youtube_url', env('YOUTUBE_URL', '#'));

  // Resolve dashboard link for logged-in users
  $dashRoute = url('/');
  if (auth()->check()) {
      $user = auth()->user();
      $isAdmin = method_exists($user, 'hasRole')
                  ? $user->hasRole('admin')
                  : (strtolower((string)($user->role ?? '')) === 'admin');
      $dashRoute = $isAdmin && Route::has('admin.dashboard')
                  ? route('admin.dashboard')
                  : (Route::has('users.dashboard') ? route('users.dashboard') : url('/'));
  }
@endphp

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>92 Dream PK — Buy Tickets. Watch Live. Win Prizes.</title>
   <link rel="icon" type="image/png" href="{{ asset('asset/images/LOGO LUCKY DRAW.png') }}" sizes="16x16" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --brand:#4f46e5;     /* Indigo */
      --accent:#0ea5e9;    /* Sky */
      --dark:#0f172a;      /* Slate-900 */
      --muted:#64748b;     /* Slate-500 */
      --bg:#f7f9fc;        /* App background */
    }

    html{scroll-padding-top:72px;}
    body{ background:var(--bg); color:var(--dark); -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility; }
    img{ max-width:100%; height:auto; display:block; }

    .container-xl{ max-width:1200px; }

    /* Header */
    .landing-header{
      position:sticky; top:0; z-index:1040; background:#fff;
      border-bottom:1px solid #eef0f4;
    }
    .brand{ display:flex; align-items:center; gap:.6rem; text-decoration:none; color:var(--dark); font-weight:800; }
    .brand img{ height:40px; width:auto; }
    .landing-header .nav-link{ color:#475569; font-weight:600; }
    .landing-header .nav-link:hover{ color:var(--brand); }

    .btn-brand{
      background:linear-gradient(135deg,var(--brand),var(--accent));
      color:#fff; font-weight:700; border:0;
      box-shadow:0 10px 24px rgba(14,165,233,.28);
    }
    .btn-brand:hover{ opacity:.95; color:#fff; }

    /* Offcanvas (mobile menu) */
    .offcanvas .nav-link{ font-weight:600; color:#334155; }
    .offcanvas .nav-link:hover{ color:var(--brand); }

    /* Hero */
    .hero{
      position:relative; overflow:hidden; background:#0b1020; color:#fff;
    }
    .hero .overlay{ position:absolute; inset:0;
      background:linear-gradient( to right, rgba(6,10,25,.78), rgba(10,18,40,.55) );
    }
    .hero img.bg{
      position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.35;
      transform:scale(1.03); filter:saturate(110%) contrast(105%);
    }
    .hero .content{ position:relative; z-index:2; padding:64px 0 56px; }
    .hero h1{
      font-weight:900; line-height:1.15;
      font-size: clamp(1.45rem, 1.05rem + 3.4vw, 3rem);
      letter-spacing:.2px;
      margin-bottom:.75rem;
      word-break:break-word;
    }
    .hero p.lead{ color:#dbe3ff; font-size:clamp(.98rem, .9rem + .6vw, 1.2rem); }

    /* Stats row */
    .stat-badge{
      background:#fff; border:1px solid #eef0f4; border-radius:16px; padding:16px;
      box-shadow:0 8px 34px rgba(2,16,60,.06);
    }
    .stat-badge .value{ font-weight:900; font-size:clamp(1rem,.7rem + 1vw,1.35rem); color:var(--brand); }
    .stat-badge .label{ color:var(--muted); font-weight:600; letter-spacing:.3px; font-size:.9rem; }

    /* Generic cards */
    .section-title{ font-weight:900; letter-spacing:.2px; font-size:clamp(1.15rem,.9rem + 1.2vw,1.75rem); }
    .card-lite{
      background:#fff; border:1px solid #eef0f4; border-radius:16px;
      box-shadow:0 8px 34px rgba(15,23,42,.06);
    }

    /* Logo bubbles */
    .logo-bubble{
      width:56px; height:56px; border-radius:50%;
      background:#fff; box-shadow:0 10px 22px rgba(2,16,60,.12);
      display:grid; place-items:center; overflow:hidden;
      flex:0 0 auto;
    }
    .logo-bubble img{ width:100%; height:100%; object-fit:contain; padding:8px; }

    /* Live stream callout */
    .callout{
      background:linear-gradient(135deg,#1d4ed8, #06b6d4);
      color:#fff; border-radius:20px;
      box-shadow:0 18px 50px rgba(2, 132, 199, .35);
    }

    /* Quote cards */
    .quote{ position:relative; padding-left:42px; }
    .quote:before{ content:"“"; position:absolute; left:0; top:-14px;
      font-size:3.2rem; line-height:1; color:#c7d2fe; font-weight:900; opacity:.5;
    }
    .quote small{ color:#cbd5e1; }

    /* CTA bottom */
    .cta-gradient{
      background:radial-gradient(1200px 400px at 10% 120%, #4f46e5 10%, #0ea5e9 40%, #06b6d4 70%);
      color:#fff; border-radius:24px;
      box-shadow:0 30px 80px rgba(79,70,229,.35);
    }

    /* Footer */
    .site-footer{ border-top:1px solid #eef0f4; background:#fff; color:#6b7280; }

    /* Hero right mockup */
    .mockup{
      background:#fff; border:1px solid #eef0f4; border-radius:20px;
      box-shadow:0 10px 36px rgba(2,16,60,.10);
      height:240px; padding:0; display:flex; align-items:center; justify-content:center;
      overflow:hidden;
    }
    .mockup img{
      width:100%; height:100%; object-fit:contain; object-position:center; border-radius:20px;
    }
    @media (min-width:768px){ .mockup{ height:300px; } }
    @media (min-width:1200px){ .mockup{ height:360px; } }

    /* ======= Mobile-first fixes ======= */
    /* Very small phones (≤360px) */
    @media (max-width:360px){
      .container-xl{ padding-left:12px; padding-right:12px; }
      .brand img{ height:32px; }
      .hero .content{ padding:48px 0 36px; }
      .hero h1{ font-size: clamp(1.25rem, 1.05rem + 3.2vw, 2.2rem); }
      .hero p.lead{ font-size: .98rem; }
      .btn, .btn-lg{ padding:.625rem .9rem; font-size:.95rem; }
      .logo-bubble{ width:48px; height:48px; }
      .stat-badge{ padding:12px; border-radius:12px; }
      .quote{ padding-left:32px; }
      .quote:before{ top:-10px; font-size:2.6rem; }
      /* hide brand text to save space */
      .brand span{ display:none; }
    }

    /* Small devices (≤576px) */
    @media (max-width:575.98px){
      /* stack & expand CTAs */
      .hero .content .d-flex.gap-2 > a{
        width:100%;
      }
      /* tighter rows */
      .row.g-4{ --bs-gutter-x:1rem; --bs-gutter-y:1rem; }
      .section-title{ margin-bottom:.75rem; }
    }

    /* Tablet tweaks (≤768px) */
    @media (max-width:767.98px){
      .hero .content{ padding:56px 0 44px; }
    }
  </style>
</head>
<body>

  {{-- ================= Header ================= --}}
  <header class="landing-header">
    <div class="container-xl py-2">
      <div class="d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}" class="brand">
          <img src="{{ $logo }}" alt="92 Dream PK logo"> <span class="d-none d-sm-inline">92 Dream PK</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="d-none d-md-flex align-items-center gap-3">
          <a href="#how" class="nav-link">How it Works</a>
          <a href="#why" class="nav-link">Why Us</a>
          <a href="https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a" class="nav-link">Live Stream</a>
          <a href="#faq" class="nav-link">FAQ</a>

          @auth
            <a href="{{ $dashRoute }}" class="btn btn-brand ms-1">Dashboard</a>
          @else
            <a href="{{ route('registerform') }}" class="btn btn-outline-primary fw-semibold">Register</a>
            <a href="{{ route('loginform') }}" class="btn btn-brand ms-2">Login</a>
          @endauth
        </nav>

        {{-- Mobile toggler --}}
        <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#siteMenu" aria-controls="siteMenu" aria-label="Open menu">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>
  </header>

  {{-- Mobile offcanvas menu --}}
  <div class="offcanvas offcanvas-end" tabindex="-1" id="siteMenu" aria-labelledby="siteMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="siteMenuLabel">Menu</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column gap-2">
      <a class="nav-link" href="#how" data-bs-dismiss="offcanvas">How it Works</a>
      <a class="nav-link" href="#why" data-bs-dismiss="offcanvas">Why Us</a>
      <a class="https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a" href="#live" data-bs-dismiss="offcanvas">Live Stream</a>
      <a class="nav-link" href="#faq" data-bs-dismiss="offcanvas">FAQ</a>
      <hr>
      @auth
        <a href="{{ $dashRoute }}" class="btn btn-brand">Dashboard</a>
      @else
        <a href="{{ route('registerform') }}" class="btn btn-outline-primary">Register</a>
        <a href="{{ route('loginform') }}" class="btn btn-brand">Login</a>
      @endauth
    </div>
  </div>

  {{-- ================= Hero ================= --}}
  <section class="hero">
    <img class="bg" src="{{ $heroImg }}" alt="Live Lottery background">
    <div class="overlay"></div>

    <div class="container-xl content">
      <div class="row align-items-center g-4">
        <div class="col-md-7">
          <h1>Buy Tickets. Watch Live. Win Prizes.</h1>
          <p class="lead mb-4">
            Join thousands of players buying tickets for transparent live draws on YouTube.
            Sales close → we stream → winners get paid fast. Simple.
          </p>

          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('registerform') }}" class="btn btn-brand btn-lg px-4">Buy Lucky Draw Ticket</a>
            <a href="https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg px-4">
              Watch Live Stream
            </a>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-6 col-sm-4">
              <div class="stat-badge text-center h-100">
                <div class="value">100% Live</div>
                <div class="label">Transparent Draws</div>
              </div>
            </div>
            <div class="col-6 col-sm-4">
              <div class="stat-badge text-center h-100">
                <div class="value">Instant</div>
                <div class="label">Ticket Purchase</div>
              </div>
            </div>
            <div class="col-12 col-sm-4">
              <div class="stat-badge text-center h-100">
                <div class="value">Fast</div>
                <div class="label">Prize Payouts</div>
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT: white box with image (visible from md and up) --}}
        <div class="col-md-5 d-none d-md-block">
          <div class="mockup">
            <img src="{{ asset('asset/images/Home New LUCKYDRAW.png') }}"
                 alt="Live draw / product preview" loading="lazy">
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= How it works ================= --}}
  <section id="how" class="py-5">
    <div class="container-xl">
      <h2 class="section-title mb-4">How It Works</h2>

      <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card-lite p-4 h-100 d-flex align-items-start gap-3">
            <div class="logo-bubble"><img src="{{ $icoCreate }}" alt="Create"></div>
            <div>
              <h6 class="fw-bold mb-1">1. Create Account</h6>
              <p class="mb-0 text-muted">Register & log in to your personal dashboard.</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card-lite p-4 h-100 d-flex align-items-start gap-3">
            <div class="logo-bubble"><img src="{{ $icoBuy }}" alt="Buy"></div>
            <div>
              <h6 class="fw-bold mb-1">2. Get Lucky Draw Ticket</h6>
              <p class="mb-0 text-muted">Get perfume and get lucky draw tickets</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card-lite p-4 h-100 d-flex align-items-start gap-3">
            <div class="logo-bubble"><img src="{{ $icoWatch }}" alt="Watch"></div>
            <div>
              <h6 class="fw-bold mb-1">3. Watch Live</h6>
              <p class="mb-0 text-muted">We stream the draw on YouTube—fully transparent.</p>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card-lite p-4 h-100 d-flex align-items-start gap-3">
            <div class="logo-bubble"><img src="{{ $icoPaid }}" alt="Paid"></div>
            <div>
              <h6 class="fw-bold mb-1">4. Get Paid</h6>
              <p class="mb-0 text-muted">Winners are verified quickly and prizes are paid fast.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= Why choose us ================= --}}
  <section id="why" class="pb-5">
    <div class="container-xl">
      <h2 class="section-title mb-4">Why Choose 92 Dream PK?</h2>
      <div class="row g-4">
        <div class="col-12 col-md-4">
          <div class="card-lite p-4 h-100">
            <h6 class="fw-bold">Secure Payments</h6>
            <p class="mb-0 text-muted">We use trusted local channels and safeguard your data end-to-end.</p>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card-lite p-4 h-100">
            <h6 class="fw-bold">100% Live & Transparent</h6>
            <p class="mb-0 text-muted">Every draw is streamed live on YouTube. No hidden steps—watch and verify.</p>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card-lite p-4 h-100">
            <h6 class="fw-bold">Fast Payouts</h6>
            <p class="mb-0 text-muted">We aim to process payouts as quickly as possible for verified winners.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= Next live stream banner ================= --}}
  <section id="live" class="pb-5">
    <div class="container-xl">
      <div class="callout p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div>
          <h3 class="fw-bold mb-1">Next Live Stream</h3>
          <p class="mb-0">We announce the draw time once all tickets are sold. Tune in and watch the action live!</p>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
          <a href="https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a" class="btn btn-light fw-bold px-4" target="_blank" rel="noopener">
            Go to YouTube
          </a>
          <a href="{{ route('registerform') }}" class="btn btn-outline-light fw-bold px-4">
            Get Started
          </a>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= Testimonials ================= --}}
  

  {{-- ================= FAQ ================= --}}
  <section id="faq" class="pb-5">
    <div class="container-xl">
      <h2 class="section-title mb-4">Frequently Asked Questions</h2>

      <div class="accordion" id="faqAcc">
        <div class="accordion-item card-lite">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
              Is the draw really live?
            </button>
          </h2>
          <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              Yes. Every draw is streamed on YouTube. You can watch, verify, and celebrate in real-time.
            </div>
          </div>
        </div>

        <div class="accordion-item card-lite mt-3">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
              Can I Get multiple tickets?
            </button>
          </h2>
          <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              Absolutely. You can purchase unlimited tickets while sales are open.
            </div>
          </div>
        </div>

        <div class="accordion-item card-lite mt-3">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
              How are winners paid?
            </button>
          </h2>
          <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body">
              After verification, we process prize payouts quickly using secure payment methods.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= Final CTA ================= --}}
  <section class="pb-5">
    <div class="container-xl">
      <div class="cta-gradient p-4 p-lg-5 text-center">
        <h2 class="fw-bold mb-2">Ready to Join the Next Draw?</h2>
        <p class="mb-4">Create your account, get tickets, and catch the action live on YouTube.</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
          <a href="{{ route('registerform') }}" class="btn btn-light fw-bold px-4">Create Account</a>
          <a href="{{ route('loginform') }}" class="btn btn-outline-light fw-bold px-4">Log In</a>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= Footer ================= --}}
  <footer class="site-footer py-4">
    <div class="container-xl d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div>© {{ date('Y') }} 92 Dream PK. All rights reserved.</div>
      <div class="d-flex align-items-center gap-3">
        <a href="#how" class="text-decoration-none">How it works</a>
        <a href="#why" class="text-decoration-none">Why Us</a>
        <a href="https://youtube.com/@92dreampk?si=hf6OmC6i3GSw7W3a" class="text-decoration-none">Live Stream</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
