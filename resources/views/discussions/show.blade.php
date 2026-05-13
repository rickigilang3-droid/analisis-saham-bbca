@extends('layouts.app')
@section('content')
<div class="container">
    <h2>{{ $discussion->title }}</h2>
    <p>{{ $discussion->content }}</p>
    <small>oleh {{ $discussion->user->name ?? 'User' }} | {{ $discussion->created_at->diffForHumans() }}</small>
    <hr>
    <h5>Komentar</h5>
    <ul>
        @foreach($discussion->comments as $c)
            <li><b>{{ $c->user->name ?? 'User' }}:</b> {{ $c->content }} <small>{{ $c->created_at->diffForHumans() }}</small></li>
        @endforeach
    </ul>
    <form method="POST" action="{{ route('discussions.comment', $discussion->id) }}">
        @csrf
        <textarea name="content" placeholder="Tulis komentar..." required class="form-control mb-2"></textarea>
        <button type="submit" class="btn btn-success">Kirim Komentar</button>
    </form>
    <a href="{{ route('discussions.index') }}" class="btn btn-link mt-3">&larr; Kembali ke Diskusi</a>
</div>
@endsection
