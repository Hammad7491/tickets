@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

  {{-- Title + back --}}
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">{{ $isEdit ? 'Edit Winner' : 'Create Winner' }}</h2>
      <div class="text-muted">Fill in the details and save.</div>
    </div>
    <a href="{{ route('admin.winners.index') }}" class="btn btn-light">
      <i class="bi bi-arrow-left me-1"></i> Back to list
    </a>
  </div>

  {{-- flashes --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Fix the errors below:</strong>
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">
      <form
        action="{{ $isEdit ? route('admin.winners.update', $winner) : route('admin.winners.store') }}"
        method="POST"
        autocomplete="off"
      >
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
          <div class="col-md-5">
            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="e.g., John Doe"
                   value="{{ old('name', $winner->name) }}"
                   required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
            <input type="text"
                   name="serial_number"
                   class="form-control @error('serial_number') is-invalid @enderror"
                   placeholder="e.g., PK123456"
                   value="{{ old('serial_number', $winner->serial_number) }}"
                   required>
            @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-3">
            <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">Rs</span>
              <input type="number"
                     name="price"
                     class="form-control @error('price') is-invalid @enderror"
                     step="0.01"
                     min="0"
                     max="99999999.99"
                     placeholder="0.00"
                     value="{{ old('price', $winner->price) }}"
                     required>
              @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <a href="{{ route('admin.winners.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2 me-1"></i> {{ $isEdit ? 'Update Winner' : 'Create Winner' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
