@extends('layouts.admin')
@section('pageTitle', 'Create New Record')

@section('breadcrumbs', Breadcrumbs::render('new-record'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Trainee') }}</div>

                <div class="card-body">
                    <form method="POST" action="/create-new-record">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Full Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="internship_start" class="col-md-4 col-form-label text-md-end">{{ __('Internship Start Date') }}</label>

                            <div class="col-md-6">
                                <input id="internship_start" type="date" class="form-control" name="internship_start" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="internship_end" class="col-md-4 col-form-label text-md-end">{{ __('Internship End Date') }}</label>

                            <div class="col-md-6">
                                <input id="internship_end" type="date" class="form-control" name="internship_end" required>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
