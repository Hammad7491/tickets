<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up | {{ config('app.name','Silva') }}</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" sizes="16x16" />

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
      --brand:#4f46e5;
      --accent:#06b6d4;
      --ink:#0b132b;
      --muted:#64748b;
    }

    html, body { height:100%; }
    body { overflow:hidden; }

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
      height:52px; border-radius:12px!important; border:1px solid #e5e7eb; background:#f8fafc;
    }
    .form-control:focus{
      border-color:#bfdbfe; box-shadow:0 0 0 .18rem rgba(59,130,246,.12); background:#fff;
    }
    /* Stronger invalid visuals */
    .form-control.is-invalid{
      border-color:#ef4444 !important;
      box-shadow:0 0 0 .18rem rgba(239,68,68,.12) !important;
      background:#fff !important;
    }

    .btn-primary{
      background: linear-gradient(90deg, var(--brand), var(--accent), var(--brand));
      background-size:200% 100%;
      animation: slide 4s linear infinite;
      border:0; box-shadow: 0 14px 32px rgba(6,182,212,.28);
      font-weight:800; letter-spacing:.2px;
    }
    @keyframes slide { to { background-position:-200% 0; } }

    .pw-meter{ height:6px; border-radius:999px; background:#e5e7eb; overflow:hidden; }
    .pw-meter > span{ display:block; height:100%; width:0; background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e); transition:width .25s ease; }

    .chip-row{ gap:.4rem; }
    .chip{
      display:inline-flex; align-items:center; gap:.35rem;
      background:#eef2ff; color:#4338ca; font-weight:600; font-size:.82rem;
      border-radius:999px; padding:.25rem .55rem; border:1px solid #e2e8f0;
    }

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
        <img class="auth-logo" src="{{ asset('asset/images/logo_92.png') }}" alt="logo">
      </a>
    </div>

    {{-- Title --}}
    <div class="text-center mb-3">
      <h4 class="auth-title mb-1">Create your account</h4>
      <p class="auth-sub mb-0">Sign up to continue to {{ config('app.name','Silva') }}.</p>
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
    <form action="{{ route('register') }}" method="POST" autocomplete="off" id="registerForm" novalidate>
      @csrf

      {{-- Name --}}
      <div class="icon-field mb-3 position-relative">
        <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
          <iconify-icon icon="ri:user-line"></iconify-icon>
        </span>
        <input type="text" name="name" class="form-control ps-5" placeholder="Name" required value="{{ old('name') }}">
      </div>

      {{-- Email --}}
      <div class="icon-field mb-3 position-relative">
        <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
          <iconify-icon icon="mage:email"></iconify-icon>
        </span>
        <input type="email" name="email" class="form-control ps-5" placeholder="Email address" required value="{{ old('email') }}">
      </div>

      {{-- Phone (exact 11 digits) --}}
      <div class="icon-field mb-1 position-relative">
        <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
          <iconify-icon icon="ri:phone-line"></iconify-icon>
        </span>
        <input
          type="tel"
          name="phone"
          id="phone"
          class="form-control ps-5"
          placeholder="03XXXXXXXXX"
          inputmode="numeric"
          autocomplete="tel"
          aria-describedby="phoneError"
          value="{{ old('phone') }}"
          required
          pattern="\d{11}"
          title="Enter exactly 11 digits"
        >
      </div>
      <div id="phoneError" class="invalid-feedback"></div>

      {{-- Password --}}
      <div class="position-relative mb-2">
        <div class="icon-field position-relative">
          <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
          </span>
          <input type="password" name="password" id="password" class="form-control ps-5 pe-5" placeholder="Password" minlength="6" required>
          <button type="button" class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light" data-toggle="#password"></button>
        </div>
      </div>

      {{-- Password meter --}}
      <div class="pw-meter mb-3"><span id="pwBar"></span></div>

      {{-- Confirm Password --}}
      <div class="position-relative mb-3">
        <div class="icon-field position-relative">
          <span class="icon position-absolute top-50 start-0 translate-middle-y ms-3">
            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
          </span>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control ps-5 pe-5" placeholder="Confirm password" minlength="6" required>
          <button type="button" class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-3 text-secondary-light" data-toggle="#password_confirmation"></button>
        </div>
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn btn-primary w-100 py-3 mb-2">Sign Up</button>

      <div class="d-flex flex-wrap justify-content-center chip-row mb-1">
        <span class="chip"><i class="ri-shield-check-line"></i> Secure</span>
        <span class="chip"><i class="ri-broadcast-line"></i> Live Draws</span>
        <span class="chip"><i class="ri-flashlight-line"></i> Fast Payouts</span>
      </div>

      <p class="text-center mb-0">
        Already have an account?
        <a href="{{ Route::has('login') ? route('login') : route('loginform') }}" class="text-primary fw-semibold">Log In</a>
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
script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/js/lib/magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/prism.js') }}"></script>
<script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
<script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
  // === Show/hide password
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function(){
      this.classList.toggle('ri-eye-off-line');
      const input = document.querySelector(this.getAttribute('data-toggle'));
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });

  // === Tiny password strength bar
  const pw  = document.getElementById('password');
  const bar = document.getElementById('pwBar');
  function scorePassword(s){
    let score = 0;
    if(!s) return 0;
    const variations = {
      digits: /\d/.test(s),
      lower: /[a-z]/.test(s),
      upper: /[A-Z]/.test(s),
      nonWords: /\W/.test(s),
      long: s.length >= 12
    };
    score += Math.min(6, s.length) * 5;
    score += Object.values(variations).filter(Boolean).length * 10;
    return Math.min(100, score);
  }
  pw && pw.addEventListener('input', () => { bar.style.width = scorePassword(pw.value) + '%'; });

  // === PHONE VALIDATION (exact length) ===
  (function(){
    const REQUIRED_LEN = 11; // change if you need a different exact length
    const form   = document.getElementById('registerForm');
    const input  = document.getElementById('phone');
    const errEl  = document.getElementById('phoneError');

    if(!input) return;

    // Sanitize to digits-only on input & paste, but DO NOT cap length so we can show "too long" error
    const sanitize = (val) => (val || '').replace(/\D+/g,'');
    const setError = (msg) => {
      input.classList.add('is-invalid');
      if (errEl){ errEl.textContent = msg; errEl.classList.add('d-block'); }
    };
    const clearError = () => {
      input.classList.remove('is-invalid');
      if (errEl){ errEl.textContent = ''; errEl.classList.remove('d-block'); }
    };

    function validatePhone(){
      // keep digits only in the field
      const raw = input.value;
      const digits = sanitize(raw);
      if (raw !== digits) input.value = digits;

      let msg = '';
      if (digits.length === 0) {
        msg = 'Phone number is required.';
      } else if (digits.length < REQUIRED_LEN) {
        msg = `Phone number is too short. Enter exactly ${REQUIRED_LEN} digits.`;
      } else if (digits.length > REQUIRED_LEN) {
        msg = `Phone number is too long. Enter exactly ${REQUIRED_LEN} digits.`;
      }

      if (msg){ setError(msg); return false; }
      clearError(); return true;
    }

    input.addEventListener('input', validatePhone);
    input.addEventListener('blur', validatePhone);
    input.addEventListener('paste', (e) => {
      requestAnimationFrame(validatePhone);
    });

    form.addEventListener('submit', function(e){
      const ok = validatePhone();
      if (!ok) {
        e.preventDefault();
        e.stopPropagation();
        input.focus();
      }
    });
  })();
</script>

</body>
</html>
