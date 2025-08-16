@extends('layouts.app')

@section('content')
<div class="container py-4 py-lg-5">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">Winners</h2>
      <div class="text-muted">
       
      </div>
    </div>

    @php
      $isAdminView = $isAdminView
        ?? (auth()->check() && (
              (method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin')) ||
              (strtolower((string)(auth()->user()->role ?? '')) === 'admin')
            ));
    @endphp

    @if($isAdminView)
      <a href="{{ route('admin.winners.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Winner
      </a>
    @endif
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($winners->count())
    @php
      // Controller should sort ASC by created_at so Winner 1 is the oldest.
      $offset = ($winners->currentPage() - 1) * $winners->perPage();
    @endphp

    <div class="table-responsive">
      <table class="table align-middle table-hover mb-0 winner-table">
        <thead class="table-light">
          <tr>
            <th style="width: 110px;">Rank</th>
            <th>Winner</th>
            <th style="width: 160px;">Serial</th>
            <th style="width: 160px;">Price</th>
            <th style="width: 200px;">Added</th>
            @if($isAdminView)
              <th class="text-end" style="width: 180px;">Actions</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach($winners as $i => $w)
            @php
              $rank = $offset + $i + 1; // Winner 1, 2, 3...
              $rankClass = match(true) {
                $rank === 1 => 'rank-gold',
                $rank === 2 => 'rank-silver',
                $rank === 3 => 'rank-bronze',
                default      => 'rank-default',
              };
            @endphp
            <tr class="bg-white">
              <td data-label="Rank">
                <span class="badge winner-rank {{ $rankClass }}">
                  <i class="bi bi-trophy-fill me-1"></i> Winner {{ $rank }}
                </span>
              </td>

              <td data-label="Winner" class="fw-bold winner-name">
                {{ $w->name }}
              </td>

              <td data-label="Serial">
                <span class="badge serial-badge">
                  <i class="bi bi-hash me-1"></i>{{ $w->serial_number }}
                </span>
              </td>

              <td data-label="Price">
                <span class="price-badge">Rs {{ number_format($w->price, 2) }}</span>
              </td>

              <td data-label="Added" class="text-muted">
                {{ $w->created_at?->format('d M Y, h:i A') }}
              </td>

              @if($isAdminView)
                <td data-label="Actions" class="text-end">
                  <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                    <a href="{{ route('admin.winners.edit', $w) }}" class="btn btn-outline-primary">
                      <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.winners.destroy', $w) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Delete this winner?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $winners->links() }}
    </div>
  @else
    <div class="text-center text-muted py-5">
      <div class="display-6 mb-2">No Winners Yet</div>
      <p class="mb-0">Once someone is added, they’ll appear here as <strong>Winner 1</strong>.</p>
    </div>
  @endif
</div>

<style>
  .winner-table td, .winner-table th { vertical-align: middle; }

  .winner-rank{
    border-radius:999px; color:#fff; font-weight:800; letter-spacing:.3px;
    padding:.45rem .7rem; text-transform:uppercase; font-size:.8rem;
    display:inline-flex; align-items:center; gap:.35rem;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
    white-space:nowrap;
  }
  .rank-gold   { background:linear-gradient(135deg,#F59E0B,#F7B500); }
  .rank-silver { background:linear-gradient(135deg,#94A3B8,#A3B8CC); }
  .rank-bronze { background:linear-gradient(135deg,#B45309,#C08457); }
  .rank-default{ background:linear-gradient(135deg,#4F46E5,#5B8CFF); }

  .serial-badge{
    background:#f6f7fb; color:#111827; border:1px solid #e6e9f2;
    border-radius:999px; padding:.35rem .6rem; font-weight:700; font-size:.86rem;
    display:inline-flex; align-items:center; gap:.35rem; white-space:nowrap;
  }

  .price-badge{
    background:#eaf1ff; color:#1d4ed8; font-weight:700; padding:.4rem .7rem;
    border-radius:999px; font-size:.92rem; box-shadow: inset 0 0 0 1px rgba(29,78,216,.08);
    white-space:nowrap;
  }

  .winner-name{ max-width: 520px; }

  /* ===== Mobile-first responsive “stacked cards” for small screens ===== */
  @media (max-width: 767.98px){
    .table-responsive { border:0; }

    .winner-table thead { display: none; }
    .winner-table tbody, 
    .winner-table tr, 
    .winner-table td { display: block; width: 100%; }

    .winner-table tr{
      margin-bottom: 1rem;
      border: 1px solid #e9ecef;
      border-radius: .75rem;
      overflow: hidden;
      box-shadow: 0 1px 2px rgba(16,24,40,.04);
      padding-top: .25rem;
      background:#fff;
    }

    .winner-table td{
      padding: .75rem 1rem;
      border: 0 !important;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: .75rem;
    }
    .winner-table td + td { border-top: 1px solid #f1f3f5 !important; }

    .winner-table td::before{
      content: attr(data-label);
      font-weight: 600;
      color: #6c757d;
      flex: 0 0 45%;
      max-width: 45%;
      text-align: left;
    }

    .winner-name{
      max-width: none;
      white-space: normal; /* allow wrapping on small screens */
      word-break: break-word;
    }

    /* Make chips a bit smaller on phones */
    .winner-rank   { font-size:.78rem; padding:.35rem .6rem; }
    .serial-badge  { font-size:.82rem; }
    .price-badge   { font-size:.88rem; }

    /* Actions row: keep buttons to the right and full width if needed */
    td[data-label="Actions"]{
      justify-content: flex-end;
      flex-wrap: wrap;
      gap: .5rem;
    }
    td[data-label="Actions"]::before{ content: "Actions"; }
    td[data-label="Actions"] .btn-group{ width: 100%; display:flex; justify-content:flex-end; gap:.5rem; flex-wrap:wrap; }
    td[data-label="Actions"] .btn-group .btn{ flex: 1 1 auto; min-width: 120px; }
  }

  /* Tablet tweaks */
  @media (min-width: 768px) and (max-width: 991.98px){
    .winner-name{ max-width: 360px; }
  }
</style>
@endsection
