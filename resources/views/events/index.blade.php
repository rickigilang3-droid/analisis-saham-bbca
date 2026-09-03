@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Kalender Event Emiten</h2>
            <p class="text-muted mb-0">Ringkasan agenda BBCA seperti dividen, RUPS, dan laporan keuangan.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke dashboard</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Tambah event baru</h5>
            <form method="POST" action="{{ route('events.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" placeholder="Judul event" required class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipe</label>
                    <select name="type" class="form-select" required>
                        <option value="dividen">Dividen</option>
                        <option value="rups">RUPS</option>
                        <option value="laporan">Laporan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Nilai</label>
                    <input type="number" name="value" step="0.01" class="form-control" placeholder="Opsional">
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" placeholder="Deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Tambah Event</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar agenda BBCA</h5>
            @if($events->isEmpty())
                <div class="text-muted">Belum ada event yang tersimpan.</div>
            @else
                <div class="list-group">
                    @foreach($events as $e)
                        <a href="{{ route('events.show', $e->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $e->title }}</div>
                                    <div class="text-muted small">{{ $e->date }} · {{ strtoupper($e->type) }}</div>
                                    @if($e->description)
                                        <div class="text-muted small mt-1">{{ $e->description }}</div>
                                    @endif
                                </div>
                                @if($e->value)
                                    <span class="badge text-bg-success">Rp {{ number_format($e->value, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
            <div class="mt-3">{{ $events->links() }}</div>
        </div>
    </div>
</div>
@endsection
