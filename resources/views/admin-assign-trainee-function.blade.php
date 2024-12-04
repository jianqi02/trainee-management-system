@extends('layouts.admin')
@section('pageTitle', 'Assign Supervisor')

@section('breadcrumbs', Breadcrumbs::render('assign-sv-to-trainee', $trainee->name))

@section('content')
<style>
    .recommended-badge {
    display: inline-block;
    background-color: #28a745; 
    color: white;
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.5px;
    margin-left: 10px; 
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15); 
}
</style>
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
                            @foreach ($supervisors as $supervisor)
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="selected_supervisors[]" value="{{ $supervisor->name }}" id="trainee_{{ $supervisor->name }}">
                                    <label class="form-check-label" for="trainee_{{ $supervisor->name }}">
                                        {{ $supervisor->name }}
                        
                                        @if(in_array($supervisor->name, $recommendedSupervisors))
                                        <span class="recommended-badge"
                                        data-toggle="tooltip" 
                                        data-placement="top" 
                                        title="
                                        {{ $supervisor->trainee_count == 0 ? '• This supervisor currently does not have any trainees.' : '' }} 
                                        {{ $supervisor->expertise == $trainee->expertise ? '• This supervisor has the same expertise as the trainee.' : '' }} 
                                        {{ ($leastTraineeCount >= 1 && $supervisor->trainee_count == $leastTraineeCount) ? '• This supervisor has the least number of trainees.' : '' }}
                                        ">recommended</span>
                                  
                                        @endif
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
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection
