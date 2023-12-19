@extends('layouts.admin')
@section('pageTitle', 'Remove Assignment')

@section('breadcrumbs', Breadcrumbs::render('remove-sv-from-trainee', $traineeName))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Remove Assigned Supervisor For') }} {{ $traineeName }}</div>
                <div class="card-body">
                    <form action="{{ route('remove-supervisor-method') }}" method="POST">
                        @csrf
                        <input type="hidden" name="selected_trainee" value="{{ $traineeName }}">

                        <div class="row">
                            @foreach ($currentSupervisors as $supervisor)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="selected_supervisors[]" value="{{ $supervisor->supervisor->name }}" id="trainee_{{ $supervisor->supervisor->name }}">
                                        <label class="form-check-label" for="trainee_{{ $supervisor->supervisor->name }}">
                                            {{ $supervisor->supervisor->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-danger">Remove Selected Supervisors</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
