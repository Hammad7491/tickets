@extends('layouts.app')

@section('content')
<div class="container">
    <h1>403 – Forbidden</h1>
    <p>You don’t have permission to access this page.</p>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Go Back</a>
</div>
@endsection
