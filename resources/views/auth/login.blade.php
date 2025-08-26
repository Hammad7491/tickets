<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log In | {{ config('app.name','Silva') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('asset/images/LOGO LUCKY DRAW.png') }}" sizes="16x16" />

  {{-- keep your existing CSS stack --}}
  <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />

  <style>
    :root{
      --brand:#4f46e5;   /* indigo */
      --accent:#06b6d4;  /* cyan */
      --ink:#0b132b;     /* deep navy */
      --muted:#64748b;   /* slate */
    }

    /* lock page scrolling */
    html, body { height:100%; }
    body { overflow:hidden; }

    /* background */
    .auth-modern{
      min-height:100dvh;
      display:flex; align-items:center; justify-content:center;
      background:
        radial-gradient(1200px 500px at 10% -20%, rgba(79,70,229,.35) 0%, transparent 55%),
        radial-gradient(900px 500px at 110% 10%, rgba(6,182,212,.32) 0%, transparent 60%),
        linear-gradient(135deg,#0b132b 0%, #091a2e 55%, #0b1f35 100%);
      position:relative;
      padding:20px;
    }

    /* card */
    .auth-card{
      width:100%;
      max-width:520px;
      background:rgba(255,255,255,.96);
      backdrop-filter: blur(8px);
      border-radius:20px;
      padding:26px 22px;
      position:relative;
      box-shadow:
        0 26px 70px rgba(2, 8, 23, .38),
        0 0 0 1px rgba(255,255,255,.55),
        0 0 0 3px rgba(79,70,229,.07);
    }
    .auth-card:after{
      content:""; position:absolute; inset:-2px; border-radius:inherit;
      background:
        radial-gradient(180px 180px at 10% 0%, rgba(99,102,241,.28), transparent 70%),
        radial-gradient(200px 200px at 90% 0%, rgba(6,182,212,.22), transparent 70%);
      z-index:-1; filter: blur(18px);
    }

    .auth-logo{ height:64px; width:auto; object-fit:contain; filter: drop-shadow(0 10px 24px rgba(79,70,229,.28)); }
    .auth-title{
      color:#0f172a; font-weight:900; letter-spacing:.2px;
      font-size:clamp(1.35rem, 1rem + 1.4vw, 1.9rem); line-height:1.2;
    }
    .auth-sub{ color:#6b7280; }

    .icon-field .icon{ color:#94a3b8; }
    .form-control{
      height:52px; border-radius:12px!important;
      border:1px solid #e5e7eb; background:#f8fafc;
    }
    .form-control:focus{
      border-color:#bfdbfe; box-shadow:0 0 0 .18rem rgba(59,130,246,.12);
      background:#fff;
    }

    /* CTA */
    .btn-primary{
      background: linear-gradient(90deg, var(--brand), var(--accent), var(--brand));
      background-size:200% 100%;
      animation: slide 4s linear infinite;
      border:0; box-shadow: 0 14px 32px rgba(6,182,212,.28);
      font-weight:800; letter-spacing:.2px;
    }
    @keyframes slide { to { background-position:-200% 0; } }

    /* compact chips */
    .chip-row{ gap:.4rem; }
    .chip{
      display:inline-flex; align-items:center; gap:.35rem;
      background:#eef2ff; color:#4338ca; font-weight:600; font-size:.82rem;
      border-radius:999px; padding:.25rem .55rem; border:1px solid #e2e8f0;
    }

    /* shorter screens */
    @media (max-height: 720px){
      .auth-card{ padding:20px 18px; }
      .auth-logo{ height:56px; }
      .auth-title{ font-size:clamp(1.2rem, .9rem + 1.1vw, 1.6rem); }
      .form-control{ height:48px; }
      .btn-primary{ padding:.65rem 1rem!important; }
    }
    @media (max-width:575.98px){
      .auth-card{ padding:22px 16px; }
    }
  </style>
</head>
<body>

<section class="auth-modern">
  <div class="auth-card">
    {{-- Logo --}}
    <div class="text-center mb-2">
      <a href="{{ url('/') }}" class="d-inline-block">
        <img class="auth-logo" src="{{ asset('asset/images/LOGO LUCKY DRAW.png') }}" alt="logo">
      </a>
    </div>

    {{-- Title --}}
    <div class="text-center mb-3">
      <h2 class="auth-title mb-1">Welcome back</h2>
      <p class="auth-sub mb-0">Sign in to continue to {{ config('app.name','Silva') }}.</p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    {{-- Form --}}
    <form id="loginForm" action="{{ route('login') }}" method="POST" autocomplete="off">
      @csrf

      <div class="icon-field mb-3 position-relative">
        <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
          <iconify-icon icon="mage:email"></iconify-icon>
        </span>
        <input type="email"
               name="email"
               id="emailaddress"
               class="form-control ps-5"
               placeholder="Email address"
               required
               value="{{ old('email') }}">
      </div>

      <div class="icon-field mb-2 position-relative">
        <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
          <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
        </span>
        <input type="password"
               name="password"
               id="password"
               class="form-control ps-5 pe-5"
               placeholder="Password"
               required>
        <button type="button"
                id="togglePassword"
                class="ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light"
                data-toggle="#password"></button>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check style-check d-flex align-items-center">
          <input class="form-check-input border border-neutral-300" type="checkbox" id="remember" name="remember">
          <label class="form-check-label ms-2" for="remember">Remember me</label>
        </div>
        {{-- Uncomment if you have a route --}}
        {{-- <a href="{{ route('password.request') }}" class="text-primary small">Forgot password?</a> --}}
      </div>

      <button type="submit" class="btn btn-primary w-100 py-3 mb-2">
        Log In
      </button>

      {{-- Quick-fill (optional) --}}
      <div class="d-flex flex-wrap gap-2 justify-content-center mb-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillLogin('a@a','a')">Admin</button>
        {{-- <button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillLogin('u@u','a')">User</button> --}}
      </div>

      <div class="d-flex flex-wrap justify-content-center chip-row mb-2">
        <span class="chip"><i class="ri-shield-check-line"></i> Secure</span>
        <span class="chip"><i class="ri-broadcast-line"></i> Live Draws</span>
        <span class="chip"><i class="ri-flashlight-line"></i> Fast Payouts</span>
      </div>

      <p class="text-center mb-0">
        Don’t have an account?
        <a href="{{ route('register') }}" class="text-primary fw-semibold">Sign Up</a>
      </p>
    </form>
  </div>
</section>

{{-- JS --}}
<script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/js/lib/magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/prism.js') }}"></script>
<script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
<script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
  function fillLogin(email, password) {
    document.getElementById('emailaddress').value = email;
    document.getElementById('password').value = password;
    document.getElementById('loginForm').submit();
  }

  document.getElementById('togglePassword').addEventListener('click', function() {
    this.classList.toggle('ri-eye-off-line');
    const input = document.querySelector(this.getAttribute('data-toggle'));
    input.type = input.type === 'password' ? 'text' : 'password';
  });
</script>
</body>
</html>
