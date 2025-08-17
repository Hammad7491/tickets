{{-- resources/views/welcome.blade.php --}}
@php
  use Illuminate\Support\Facades\Route;

  $logo       = asset('asset/images/logo_92.png');
  $heroImg    = asset('asset/images/landing/hero.jpg');
  $heroCard   = asset('asset/images/landing/hero-card.jpg');          // NEW (image inside white box)

  // Logos for the “How it works” cards (fallback to site logo if files are missing)
  $icoCreate  = file_exists(public_path('asset/images/landing/icons/create.png'))
                  ? asset('asset/images/landing/icons/create.png') : $logo;
  $icoBuy     = file_exists(public_path('asset/images/landing/icons/buy.png'))
                  ? asset('asset/images/landing/icons/buy.png')    : $logo;
  $icoWatch   = file_exists(public_path('asset/images/landing/icons/watch.png'))
                  ? asset('asset/images/landing/icons/watch.png')  : $logo;
  $icoPaid    = file_exists(public_path('asset/images/landing/icons/paid.png'))
                  ? asset('asset/images/landing/icons/paid.png')   : $logo;

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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
/* keep your existing mockup box sizing, but make the image fit */
.mockup{
  background:#fff;
  border:1px solid #eef0f4;
  border-radius:20px;
  box-shadow:0 10px 36px rgba(2,16,60,.10);
  height:320px;                 /* your existing height */
  padding:0;                    /* no inner padding so it can use full area */
  display:flex;                 /* center the image */
  align-items:center;
  justify-content:center;
}
@media (min-width:1200px){
  .mockup{ height:340px; }
}

/* 👇 this line makes the whole image visible without cropping */
.mockup img{
  width:100%;
  height:100%;
  object-fit:contain;           /* was 'cover' */
  object-position:center center;
  border-radius:20px;           /* match the box */
  max-width:100%;
  max-height:100%;
}


    :root{
      --brand:#4f46e5;     /* Indigo */
      --accent:#0ea5e9;    /* Sky */
      --dark:#0f172a;      /* Slate-900 */
      --muted:#64748b;     /* Slate-500 */
      --bg:#f7f9fc;        /* App background */
    }
    body{ background:var(--bg); color:var(--dark); }
    .container-xl{ max-width:1200px; }

    /* Header */
    .landing-header{
      position:sticky; top:0; z-index:60; background:#fff;
      border-bottom:1px solid #eef0f4;
    }
    .brand{ display:flex; align-items:center; gap:.6rem; text-decoration:none; color:var(--dark); font-weight:800; }
    .brand img{ height:42px; width:auto; }
    .landing-header .nav-link{ color:#475569; font-weight:600; }
    .landing-header .nav-link:hover{ color:var(--brand); }

    .btn-brand{
      background:linear-gradient(135deg,var(--brand),var(--accent));
      color:#fff; font-weight:700; border:0;
      box-shadow:0 10px 24px rgba(14,165,233,.28);
    }
    .btn-brand:hover{ opacity:.95; color:#fff; }

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
    .hero .content{ position:relative; z-index:2; padding:84px 0 64px; }
    .hero h1{ font-weight:900; line-height:1.08;
      font-size: clamp(1.9rem, 1.2rem + 2.6vw, 3.2rem);
    }
    .hero p.lead{ color:#dbe3ff; }

    /* Stats row */
    .stat-badge{
      background:#fff; border:1px solid #eef0f4; border-radius:16px; padding:18px;
      box-shadow:0 8px 34px rgba(2,16,60,.06);
    }
    .stat-badge .value{ font-weight:900; font-size:1.6rem; color:var(--brand); }
    .stat-badge .label{ color:var(--muted); font-weight:600; letter-spacing:.3px; }

    /* Generic cards */
    .section-title{ font-weight:900; letter-spacing:.2px; }
    .card-lite{
      background:#fff; border:1px solid #eef0f4; border-radius:16px;
      box-shadow:0 8px 34px rgba(15,23,42,.06);
    }

    /* Logo bubbles (replaces gradient dots) */
    .logo-bubble{
      width:56px; height:56px; border-radius:50%;
      background:#fff; box-shadow:0 10px 22px rgba(2,16,60,.12);
      display:grid; place-items:center; overflow:hidden;
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
      font-size:4rem; line-height:1; color:#c7d2fe; font-weight:900; opacity:.5;
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

    /* NEW: hero white-box image holder (same size look) */
    .mockup{
      background:#fff; border:1px solid #eef0f4; border-radius:20px;
      box-shadow:0 10px 36px rgba(2,16,60,.10);
      padding:16px; height: 320px;     /* controls the box size */
    }
    .mockup img{
      width:100%; height:100%; object-fit:cover; border-radius:12px;
    }
    @media (min-width:1200px){
      .mockup{ height:340px; }
    }
  </style>
</head>
<body>

  {{-- ================= Header ================= --}}
  <header class="landing-header">
    <div class="container-xl py-2">
      <div class="d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}" class="brand">
          <img src="{{ $logo }}" alt="92 Dream PK"> <span>92 Dream PK</span>
        </a>

        <nav class="d-none d-md-flex align-items-center gap-3">
          <a href="#how" class="nav-link">How it Works</a>
          <a href="#why" class="nav-link">Why Us</a>
          <a href="#live" class="nav-link">Live Stream</a>
          <a href="#faq" class="nav-link">FAQ</a>

          @auth
            <a href="{{ $dashRoute }}" class="btn btn-brand ms-1">Dashboard</a>
          @else
            <a href="{{ route('registerform') }}" class="btn btn-outline-primary fw-semibold">Register</a>
            <a href="{{ route('loginform') }}" class="btn btn-brand ms-2">Login</a>
          @endauth
        </nav>

        <div class="d-flex d-md-none align-items-center gap-2">
          @auth
            <a href="{{ $dashRoute }}" class="btn btn-sm btn-brand">Dashboard</a>
          @else
            <a href="{{ route('loginform') }}" class="btn btn-sm btn-brand">Login</a>
          @endauth
        </div>
      </div>
    </div>
  </header>

  {{-- ================= Hero ================= --}}
  <section class="hero">
    <img class="bg" src="{{ $heroImg }}" alt="Live Lottery">
    <div class="overlay"></div>

    <div class="container-xl content">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="mb-3">Buy Tickets. Watch Live. Win Prizes.</h1>
          <p class="lead mb-4">
            Join thousands of players buying tickets for transparent live draws on YouTube.
            Sales close → we stream → winners get paid fast. Simple.
          </p>

          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('registerform') }}" class="btn btn-brand btn-lg px-4">Get Started</a>
            <a href="{{ $youtubeUrl }}" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg px-4">
              Watch Live Stream
            </a>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-6 col-md-4">
              <div class="stat-badge text-center">
                <div class="value">100% Live</div>
                <div class="label">Transparent Draws</div>
              </div>
            </div>
            <div class="col-6 col-md-4">
              <div class="stat-badge text-center">
                <div class="value">Instant</div>
                <div class="label">Ticket Purchase</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="stat-badge text-center">
                <div class="value">Fast</div>
                <div class="label">Prize Payouts</div>
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT: the white box now shows a picture at same size --}}
        <div class="col-lg-5 d-none d-lg-block">
         <div class="mockup">
  <img src="{{ asset('asset/images/Home Page.png') }}"
       alt="Live draw / product preview" loading="lazy"
       style="max-width:90%;max-height:90%;object-fit:contain;">
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
        <div class="col-md-6 col-lg-3">
          <div class="card-lite p-4 h-100">
            <div class="logo-bubble mb-3">
              <img src="{{ $icoCreate }}" alt="Create">
            </div>
            <h6 class="fw-bold">1. Create Account</h6>
            <p class="mb-0 text-muted">Register & log in to your personal dashboard.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card-lite p-4 h-100">
            <div class="logo-bubble mb-3">
              <img src="{{ $icoBuy }}" alt="Buy">
            </div>
            <h6 class="fw-bold">2. Buy Tickets</h6>
            <p class="mb-0 text-muted">Purchase any number of tickets before the draw closes.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card-lite p-4 h-100">
            <div class="logo-bubble mb-3">
              <img src="{{ $icoWatch }}" alt="Watch">
            </div>
            <h6 class="fw-bold">3. Watch Live</h6>
            <p class="mb-0 text-muted">We stream the draw on YouTube—fully transparent.</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card-lite p-4 h-100">
            <div class="logo-bubble mb-3">
              <img src="{{ $icoPaid }}" alt="Paid">
            </div>
            <h6 class="fw-bold">4. Get Paid</h6>
            <p class="mb-0 text-muted">Winners are verified quickly and prizes are paid fast.</p>
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
        <div class="col-md-4">
          <div class="card-lite p-4 h-100">
            <h6 class="fw-bold">Secure Payments</h6>
            <p class="mb-0 text-muted">We use trusted local channels and safeguard your data end-to-end.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-lite p-4 h-100">
            <h6 class="fw-bold">100% Live & Transparent</h6>
            <p class="mb-0 text-muted">Every draw is streamed live on YouTube. No hidden steps—watch and verify.</p>
          </div>
        </div>
        <div class="col-md-4">
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
        <div class="d-flex align-items-center gap-2">
          <a href="{{ $youtubeUrl }}" class="btn btn-light fw-bold px-4" target="_blank" rel="noopener">
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
  <section class="pb-5">
    <div class="container-xl">
      <h2 class="section-title mb-4">What Players Say</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card-lite p-4 h-100 quote">
            <p class="mb-3">Transparent, fast and fun. I love watching the live streams!</p>
            <small>— Ali R.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-lite p-4 h-100 quote">
            <p class="mb-3">Buying tickets is super easy and payouts are quick. Highly recommend.</p>
            <small>— Sana K.</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card-lite p-4 h-100 quote">
            <p class="mb-3">The whole experience feels professional and secure. Great job!</p>
            <small>— Hamza M.</small>
          </div>
        </div>
      </div>
    </div>
  </section>

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
            Can I buy multiple tickets?
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
      <div class="cta-gradient p-5 text-center">
        <h2 class="fw-bold mb-2">Ready to Join the Next Draw?</h2>
        <p class="mb-4">Create your account, buy tickets, and catch the action live on YouTube.</p>
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
        <a href="#live" class="text-decoration-none">Live Stream</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
