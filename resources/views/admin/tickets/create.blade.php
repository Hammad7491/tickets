{{-- resources/views/admin/tickets/create.blade.php (used for both create & edit) --}}
@extends('layouts.app')

@section('content')
@php
  /** @var \App\Models\Ticket|null $ticket */
  $isEdit = isset($ticket);
  $title  = $isEdit ? 'Edit Ticket' : 'Create Ticket';
@endphp

<div class="container my-5">
  <div class="card border-0 shadow rounded-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="bi bi-ticket-perforated me-2"></i>{{ $title }}
      </h5>
      <a href="{{ route('admin.tickets.index') }}" class="btn btn-light btn-sm">
        <i class="bi bi-list-ul me-1"></i> Tickets
      </a>
    </div>

    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Please fix the following:</strong>
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form
        action="{{ $isEdit ? route('admin.tickets.update', $ticket->id) : route('admin.tickets.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="row g-4"
        autocomplete="off"
      >
        @csrf
        @if($isEdit)
          @method('PUT')
        @endif

        {{-- Ticket Name (required) --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Ticket Name <span class="text-danger">*</span></label>
          <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $isEdit ? $ticket->name : '') }}"
            required
            maxlength="120"
            placeholder="e.g., Concert Pass"
          >
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Total Tickets (required) --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Total Tickets <span class="text-danger">*</span></label>
          <input
            type="number"
            name="quantity"
            class="form-control @error('quantity') is-invalid @enderror"
            value="{{ old('quantity', $isEdit ? $ticket->quantity : '') }}"
            required
            min="1"
            step="1"
            placeholder="e.g., 100"
          >
          @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Picture (required) --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold">Picture <span class="text-danger">*</span></label>
          <input
            type="file"
            name="image"
            class="form-control @error('image') is-invalid @enderror"
            accept=".jpg,.jpeg,.png"
            required
          >
          <div class="form-text">JPG/PNG up to 2MB.</div>
          @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Existing image preview (edit mode) --}}
        @if($isEdit && !empty($ticket->image_path))
          <div class="col-md-6">
            <label class="form-label fw-semibold d-block">Current Image</label>
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('admin.tickets.image', ['path' => $ticket->image_path]) }}"
                 target="_blank" rel="noopener" class="d-inline-block" title="Open image in new tab">
                <img
                  src="{{ route('admin.tickets.image', ['path' => $ticket->image_path]) }}"
                  alt="Ticket image"
                  class="rounded border"
                  style="width:120px;height:80px;object-fit:cover"
                >
              </a>
              <a href="{{ route('admin.tickets.download', ['path' => $ticket->image_path]) }}"
                 class="btn btn-outline-secondary btn-sm" title="Download image">
                <i class="bi bi-download me-1"></i> Download
              </a>
            </div>
            <div class="form-text">Uploading a new file will replace this image.</div>
          </div>
        @endif

        <div class="col-12 d-flex justify-content-end gap-2">
          <a href="{{ route('admin.tickets.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update Ticket' : 'Save Ticket' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  if (window.bootstrap && bootstrap.Tooltip) {
    [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => new bootstrap.Tooltip(el));
  }
</script>
@endsection
