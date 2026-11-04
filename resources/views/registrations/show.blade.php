@extends('layouts.app')

@section('title', 'Registration Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Registration Details</h1>
        <a href="{{ route('registrations.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Student</dt>
                <dd class="col-sm-9">{{ $registration->student->first_name ?? '' }} {{ $registration->student->last_name ?? '' }}</dd>
                <dt class="col-sm-3">Course</dt>
                <dd class="col-sm-9">{{ $registration->course->title ?? 'Unknown course' }}</dd>
                <dt class="col-sm-3">Registered On</dt>
                <dd class="col-sm-9">{{ $registration->created_at->format('d M Y H:i') }}</dd>
            </dl>
        </div>
    </div>
@endsection
