@extends('layouts.admin')
@section('pageTitle', 'Assign Supervisor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Assign Supervisor For') }} {{ $trainee->name }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <form action="{{ route('supervisor-assign-method') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selected_trainee" value="{{ $trainee->name }}">

                                @foreach ($filteredSupervisors as $supervisor)
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" name="selected_supervisors[]" value="{{ $supervisor->name }}" id="trainee_{{ $supervisor->name }}">
                                        <label class="form-check-label" for="trainee_{{ $supervisor->name }}">
                                            {{ $supervisor->name }}
                                        </label>
                                    </div>
                                @endforeach

                                <button type="submit" class="btn btn-primary">Assign Selected Supervisor</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <p>Selected Supervisor(s):</p>
                            <pre id="selected-trainees-display"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
