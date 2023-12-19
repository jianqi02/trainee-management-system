@extends('layouts.sv')
@section('pageTitle', 'Comment')

@section('breadcrumbs', Breadcrumbs::render('sv-comment', $trainee->name))

@section('content')
<div class="container mt-4" style="margin-left: 135px; width: 1054px;">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Write a Comment for {{ $trainee->name }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sv-submit-comment') }}" method="POST">
                @csrf
                <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                
                <div class="mb-3">
                    <label for="comment" class="form-label">Comment:</label>
                    <textarea class="form-control" name="comment" id="comment" rows="5" required>{{ $comment }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        </div>
    </div>
</div>
@endsection
