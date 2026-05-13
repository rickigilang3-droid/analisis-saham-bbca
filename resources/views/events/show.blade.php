@extends('layouts.app')
@section('content')
<div class="container">
    <h2>{{ $event->title }}</h2>
    <p><b>Tanggal:</b> {{ $event->date }}</p>
    <p><b>Tipe:</b> {{ $event->type }}</p>
    <p>{{ $event->description }}</p>
    <a href="{{ route('events.index') }}" class="btn btn-link mt-3">&larr; Kembali ke Kalender Event</a>
</div>
@endsection
