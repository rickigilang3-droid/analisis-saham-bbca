@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Kalender Event Emiten</h2>
    <form method="POST" action="{{ route('events.store') }}">
        @csrf
        <input type="text" name="title" placeholder="Judul event" required class="form-control mb-2">
        <input type="date" name="date" required class="form-control mb-2">
        <input type="text" name="type" placeholder="Tipe (dividen, RUPS, dll)" required class="form-control mb-2">
        <textarea name="description" placeholder="Deskripsi" class="form-control mb-2"></textarea>
        <button type="submit" class="btn btn-primary">Tambah Event</button>
    </form>
    <hr>
    <ul>
        @foreach($events as $e)
            <li>
                <a href="{{ route('events.show', $e->id) }}">{{ $e->title }}</a>
                <small>{{ $e->date }} | {{ $e->type }}</small>
            </li>
        @endforeach
    </ul>
    {{ $events->links() }}
</div>
@endsection
