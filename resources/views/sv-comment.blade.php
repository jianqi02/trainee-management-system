@extends('layouts.sv')
@section('pageTitle', 'Comment')

@section('content')
<div class="container" style="margin-left: 120px;">
    <h2>Write a Comment for {{ $trainee->name }}</h2>
    <form action="{{ route('sv-submit-comment') }}" method="POST">
        @csrf
        <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea class="form-control" name="comment" id="comment" rows="5" required>{{ $comment }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Submit Comment</button>
    </form>
</div>
@endsection
