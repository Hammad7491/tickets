<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up | {{ config('app.name','Silva') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" sizes="16x16" />

    <!-- Same CSS stack as login -->
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
</head>
<body>
<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="{{ asset('assets/images/auth/auth-img.png') }}" alt="">
        </div>
    </div>

    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div class="text-center mb-40">
                <a href="{{ url('/') }}" class="max-w-290-px d-inline-block">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                </a>
            </div>

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

            <h4 class="mb-12">Create your account</h4>
            <p class="mb-32 text-secondary-light text-lg">Sign up to continue to {{ config('app.name','Silva') }}.</p>

            {{-- Social buttons (optional) --}}
            <div class="row mb-32">
                @if(Route::has('google.login'))
                <div class="col-6">
                    <a href="{{ route('google.login') }}" class="btn text-primary-600 border d-flex align-items-center justify-content-center w-100 gap-2">
                        <iconify-icon icon="logos:google-icon" class="text-xl"></iconify-icon>
                        Google
                    </a>
                </div>
                @endif
                @if(Route::has('facebook.login'))
                <div class="col-6">
                    <a href="{{ route('facebook.login') }}" class="btn text-primary-600 border d-flex align-items-center justify-content-center w-100 gap-2">
                        <iconify-icon icon="ic:baseline-facebook" class="text-xl"></iconify-icon>
                        Facebook
                    </a>
                </div>
                @endif
            </div>

            <div class="center-border-horizontal text-center mb-32">
                <span class="bg-base z-1 px-4">or continue with email</span>
            </div>

            <form action="{{ route('register') }}" method="POST" autocomplete="off" id="registerForm">
                @csrf

                {{-- Name --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="ri:user-line"></iconify-icon>
                    </span>
                    <input type="text"
                           name="name"
                           class="form-control h-56-px bg-neutral-50 radius-12"
                           placeholder="Name"
                           required
                           value="{{ old('name') }}">
                </div>

                {{-- Email --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email"
                           name="email"
                           class="form-control h-56-px bg-neutral-50 radius-12"
                           placeholder="Email address"
                           required
                           value="{{ old('email') }}">
                </div>

                {{-- Phone Number --}}
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="ri:phone-line"></iconify-icon>
                    </span>
                    <input type="tel"
                           name="phone"
                           class="form-control h-56-px bg-neutral-50 radius-12"
                           placeholder="Phone number"
                           required
                           value="{{ old('phone') }}">
                </div>

                {{-- Password --}}
                <div class="position-relative mb-16">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control h-56-px bg-neutral-50 radius-12"
                               placeholder="Password"
                               minlength="6"
                               required>
                        <button type="button"
                                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                data-toggle="#password"></button>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control h-56-px bg-neutral-50 radius-12"
                               placeholder="Confirm password"
                               minlength="6"
                               required>
                        <button type="button"
                                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                data-toggle="#password_confirmation"></button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-24">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-300" type="checkbox" id="terms" required>
                        <label class="form-check-label ms-2" for="terms">I agree to the
                            @if(Route::has('terms.show'))
                                <a href="{{ route('terms.show') }}" target="_blank" class="text-primary-600 fw-medium">Terms</a>
                            @else
                                <a href="#" class="text-primary-600 fw-medium">Terms</a>
                            @endif
                            and
                            @if(Route::has('policy.show'))
                                <a href="{{ route('policy.show') }}" target="_blank" class="text-primary-600 fw-medium">Privacy Policy</a>
                            @else
                                <a href="#" class="text-primary-600 fw-medium">Privacy Policy</a>
                            @endif
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mb-32">
                    Sign Up
                </button>

                <p class="text-center text-sm mb-0">
                    Already have an account?
                    <a href="{{ Route::has('login') ? route('login') : route('loginform') }}" class="text-primary-600 fw-semibold">Log In</a>
                </p>
            </form>
        </div>
    </div>
</section>

<!-- JS (same as login) -->
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
    // show/hide password buttons
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(){
            this.classList.toggle('ri-eye-off-line');
            const input = document.querySelector(this.getAttribute('data-toggle'));
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
</script>
</body>
</html>
