@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Diskusi Saham</h2>
    <form method="POST" action="{{ route('discussions.store') }}">
        @csrf
        <input type="text" name="title" placeholder="Judul diskusi" required class="form-control mb-2">
        <textarea name="body" placeholder="Tulis diskusi..." required class="form-control mb-2"></textarea>
        <button type="submit" class="btn btn-primary">Buat Diskusi</button>
    </form>
    <hr>
    <ul>
        @foreach($discussions as $d)
            <li>
                <a href="{{ route('discussions.show', $d->id) }}">{{ $d->title }}</a>
                <small>oleh {{ $d->user->name ?? 'User' }} | {{ $d->created_at->diffForHumans() }}</small>
            </li>
        @endforeach
    </ul>
    {{ $discussions->links() }}
</div>
@endsection
