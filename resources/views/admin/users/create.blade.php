@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">
  <!-- Header -->
  <div class="page-header-pro mb-4">
    <div class="d-flex align-items-center gap-3">
      <span class="avatar-circle"><i class="bi bi-person"></i></span>
      <div>
        <h2 class="page-title mb-1">{{ isset($user) ? 'Edit User' : 'New User' }}</h2>
        <div class="text-white-50 small">Create an account & assign roles</div>
      </div>
    </div>
  </div>

  <div class="card card-pro shadow-sm border-0 rounded-4">
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> Please fix the errors below.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form
        action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
        method="POST"
        autocomplete="off"
        class="needs-validation"
        novalidate
      >
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="row g-4">
          <!-- Name -->
          <div class="col-md-6">
            <div class="form-floating has-icon">
              <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                     id="name" name="name" placeholder="Name"
                     value="{{ old('name', $user->name ?? '') }}" maxlength="100" required>
              <label for="name"><i class="bi bi-person-fill me-2"></i>Name <span class="text-danger">*</span></label>
              <div class="invalid-feedback">Please enter a name (max 100 chars).</div>
              <small class="text-muted d-block mt-1">Max 100 characters. <span id="name-count">0</span>/100</small>
            </div>
          </div>

          <!-- Email -->
          <div class="col-md-6">
            <div class="form-floating has-icon">
              <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                     id="email" name="email" placeholder="Email"
                     value="{{ old('email', $user->email ?? '') }}" required>
              <label for="email"><i class="bi bi-envelope-fill me-2"></i>Email <span class="text-danger">*</span></label>
              <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
          </div>

          <!-- Phone -->
          <div class="col-md-6">
            <div class="form-floating has-icon">
              <input type="tel" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                     id="phone" name="phone" placeholder="Phone Number"
                     value="{{ old('phone', $user->phone ?? '') }}"
                     pattern="^\+?[0-9\s\-()]{7,20}$">
              <label for="phone"><i class="bi bi-telephone-fill me-2"></i>Phone Number</label>
              <div class="invalid-feedback">Enter a valid phone (e.g. +92 300 1234567).</div>
            </div>
          </div>

          <!-- Password -->
          <div class="col-md-6">
            <label for="password" class="form-label fw-semibold">
              <i class="bi bi-lock-fill me-1"></i>{{ isset($user) ? 'New Password (leave blank to keep)' : 'Password *' }}
            </label>
            <div class="input-group">
              <input type="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                     id="password" name="password"
                     {{ isset($user) ? '' : 'required' }} minlength="8" autocomplete="new-password">
              <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password" aria-label="Show/Hide">
                <i class="bi bi-eye"></i>
              </button>
              <div class="invalid-feedback">Password must be at least 8 characters.</div>
            </div>
            <small class="text-muted"><i class="bi bi-shield-lock me-1"></i>Use 8+ chars, mix letters, numbers & symbols.</small>
          </div>

          <!-- Confirm Password -->
          <div class="col-md-6">
            <label for="password_confirmation" class="form-label fw-semibold">
              <i class="bi bi-lock-fill me-1"></i>Confirm Password {{ isset($user) ? '' : '*' }}
            </label>
            <div class="input-group">
              <input type="password" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                     id="password_confirmation" name="password_confirmation"
                     {{ isset($user) ? '' : 'required' }} autocomplete="new-password">
              <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation" aria-label="Show/Hide">
                <i class="bi bi-eye"></i>
              </button>
              <div class="invalid-feedback">Passwords must match.</div>
            </div>
          </div>

          <!-- Roles: ONLY Admin/User -->
          <div class="col-12">
            <label class="form-label fw-semibold">
              <i class="bi bi-people-fill me-2"></i>Roles <span class="text-danger">*</span>
              <i class="bi bi-question-circle ms-1 text-muted" data-bs-toggle="tooltip" title="Select one or more roles for this user."></i>
            </label>

            <div class="dropdown w-100 role-picker">
              <button class="form-select text-start d-flex align-items-center justify-content-between"
                      type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <span class="selected-badges d-inline-flex flex-wrap gap-1"></span>
                <span class="placeholder text-muted">Select roles…</span>
                <i class="bi bi-caret-down ms-auto"></i>
              </button>

              <div class="dropdown-menu w-100 p-0 shadow" style="max-height:280px;overflow:auto;z-index:2000;">
                <div class="px-3 pt-3 pb-2">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control role-search" placeholder="Search roles…">
                  </div>
                </div>
                <hr class="my-0">
                <div class="role-list py-2">
                  @php
                    $allowed = ['admin','user'];
                  @endphp
                  @foreach($allowed as $rname)
                    <label class="dropdown-item d-flex align-items-center gap-2">
                      <input type="checkbox" class="form-check-input role-check"
                             value="{{ $rname }}"
                             {{ in_array($rname, $userRoles ?? []) ? 'checked' : '' }}>
                      <span>{{ ucfirst($rname) }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- real field Laravel reads -->
            <select id="roles" name="roles[]" multiple class="d-none">
              @foreach(['admin','user'] as $rname)
                <option value="{{ $rname }}" {{ in_array($rname, $userRoles ?? []) ? 'selected' : '' }}>
                  {{ ucfirst($rname) }}
                </option>
              @endforeach
            </select>

            <div class="invalid-feedback d-block" id="rolesError" style="display:none;">Choose at least one role.</div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
          <small class="text-muted"><i class="bi bi-lock me-1"></i>Your changes are secure & will be audited.</small>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
              <i class="bi bi-arrow-left-circle me-1"></i>Back to List
            </a>
            <button type="submit" class="btn btn-primary px-4">
              <i class="fas fa-save me-1"></i>{{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .page-header-pro{background:linear-gradient(135deg,#4f46e5,#0ea5e9);border-radius:1rem;padding:1rem 1.25rem;color:#fff}
  .avatar-circle{width:42px;height:42px;border-radius:50%;display:grid;place-items:center;background:#eef2ff;color:#4f46e5}
  .has-icon label i{opacity:.9}

  /* Eye button alignment in input-group */
  .toggle-password{display:flex;align-items:center;gap:.25rem}
  .toggle-password i{line-height:1;font-size:1rem}

  /* Role picker visuals */
  .role-picker .form-select{border-radius:.75rem; padding:.65rem .875rem; min-height:3.25rem}
  .role-picker .dropdown-menu{margin-top:.375rem}
  .role-picker .placeholder{margin-left:.25rem}
  .role-picker .selected-badges .badge{font-weight:500}
</style>

<script>
  // Tooltips if Bootstrap JS exists
  (function(){
    if (window.bootstrap && bootstrap.Tooltip) {
      [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el=>new bootstrap.Tooltip(el));
    }
  })();

  // Name counter
  (function(){
    const i=document.getElementById('name'), c=document.getElementById('name-count');
    if(!i||!c) return; const u=()=>c.textContent=i.value.length; i.addEventListener('input',u); u();
  })();

  // Show/Hide password
  document.querySelectorAll('.toggle-password').forEach(btn=>{
    btn.addEventListener('click',()=>{
      const input = document.querySelector(btn.dataset.target);
      if(!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      const icon = btn.querySelector('i');
      if(icon){ icon.classList.toggle('bi-eye', !show); icon.classList.toggle('bi-eye-slash', show); }
    });
  });

  // Form validation (roles + confirm password)
  (function(){
    const form=document.querySelector('.needs-validation');
    const pwd=document.getElementById('password');
    const pc=document.getElementById('password_confirmation');
    const rolesSelect = document.getElementById('roles');
    const rolesError = document.getElementById('rolesError');

    form?.addEventListener('submit',function(e){
      const any = [...rolesSelect.options].some(o=>o.selected);
      rolesError.style.display = any ? 'none' : 'block';
      if(!any){ e.preventDefault(); e.stopPropagation(); }

      if(pwd && pc && pwd.value!=='' && pwd.value!==pc.value){
        pc.classList.add('is-invalid'); e.preventDefault(); e.stopPropagation();
      }

      if(!form.checkValidity()){ e.preventDefault(); e.stopPropagation(); }
      form.classList.add('was-validated');
    });
  })();

  // Roles picker wiring (checkbox list <-> hidden select + badges + search)
  (function(){
    const picker = document.querySelector('.role-picker');
    if(!picker) return;

    const menu = picker.querySelector('.dropdown-menu');
    const listWrap = picker.querySelector('.role-list');
    const search = picker.querySelector('.role-search');
    const select = document.getElementById('roles');
    const badges = picker.querySelector('.selected-badges');
    const placeholder = picker.querySelector('.placeholder');

    function getChecks(){ return listWrap.querySelectorAll('.role-check'); }

    function syncFromChecks(){
      [...select.options].forEach(o=>o.selected=false);
      const picked = [];
      getChecks().forEach(ch=>{
        if(ch.checked){
          picked.push(ch.value);
          const opt = [...select.options].find(o=>o.value===ch.value);
          if(opt) opt.selected = true;
        }
      });
      renderBadges(picked);
    }

    function renderBadges(values){
      badges.innerHTML = '';
      if(!values.length){ placeholder.style.display=''; return; }
      placeholder.style.display='none';
      values.forEach(v=>{
        const b = document.createElement('span');
        b.className = 'badge text-bg-light border';
        b.textContent = v.charAt(0).toUpperCase()+v.slice(1);
        badges.appendChild(b);
      });
    }

    // Filter options
    search?.addEventListener('input',()=>{
      const q = search.value.trim().toLowerCase();
      picker.querySelectorAll('.dropdown-item').forEach(lbl=>{
        const txt = lbl.textContent.trim().toLowerCase();
        lbl.style.display = (q && !txt.includes(q)) ? 'none' : '';
      });
    });

    // Listen changes
    listWrap.addEventListener('change', e=>{
      if(e.target && e.target.classList.contains('role-check')) syncFromChecks();
    });

    // Init from pre-checked (edit mode)
    syncFromChecks();

    // Close on ESC for UX
    menu.addEventListener('keydown', e=>{
      if(e.key==='Escape'){
        const dropdown = bootstrap.Dropdown.getOrCreateInstance(picker.querySelector('[data-bs-toggle="dropdown"]'));
        dropdown.hide();
      }
    });
  })();
</script>
@endsection
