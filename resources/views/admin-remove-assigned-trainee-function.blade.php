@extends('layouts.admin')
@section('pageTitle', 'Remove Assignment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Remove Assigned Supervisor For') }} {{ $traineeName }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <form action="{{ route('remove-supervisor-method') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selected_trainee" value="{{ $traineeName }}">

                                @foreach ($currentSupervisors as $supervisor)
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" name="selected_supervisors[]" value="{{ $supervisor->supervisor->name }}" id="trainee_{{ $supervisor->supervisor->name }}">
                                        <label class="form-check-label" for="trainee_{{ $supervisor->supervisor->name }}">
                                            {{ $supervisor->supervisor->name }}
                                        </label>
                                    </div>
                                @endforeach

                                <button type="submit" class="btn btn-primary">Remove Selected Supervisors</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
