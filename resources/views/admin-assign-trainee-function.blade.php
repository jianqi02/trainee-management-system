@extends('layouts.admin')
@section('pageTitle', 'Assign Supervisor')

@section('breadcrumbs', Breadcrumbs::render('assign-sv-to-trainee', $trainee->name))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Assign Supervisor For') }} {{ $trainee->name }}</div>
                <div class="card-body">
                    <form action="{{ route('supervisor-assign-method') }}" method="POST">
                        @csrf
                        <input type="hidden" name="selected_trainee" value="{{ $trainee->name }}">

                        <div class="row">
                            @foreach ($filteredSupervisors as $supervisor)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="selected_supervisors[]" value="{{ $supervisor->name }}" id="trainee_{{ $supervisor->name }}">
                                        <label class="form-check-label" for="trainee_{{ $supervisor->name }}">
                                            {{ $supervisor->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Assign Selected Supervisors</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
