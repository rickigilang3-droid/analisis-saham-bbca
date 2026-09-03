@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="mb-1">{{ $event->title }}</h2>
                    <div class="text-muted">{{ $event->date }} · {{ strtoupper($event->type) }}</div>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>

            @if($event->description)
                <p class="mb-3">{{ $event->description }}</p>
            @endif

            @if($event->value)
                <div class="alert alert-success mb-0">
                    Nilai event: Rp {{ number_format($event->value, 0, ',', '.') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
