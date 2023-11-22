@extends('layouts.basicpage')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @if(session('error'))
                    <div class="alert alert-warning">{{ session('error') }}</div>
                @endif 
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    

                    {{ __('Welcome to Trainee Management System!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
