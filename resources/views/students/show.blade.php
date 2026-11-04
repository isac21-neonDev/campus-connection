@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Student Details</h1>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Student Number</dt>
                <dd class="col-sm-9">{{ $student->student_number }}</dd>
                <dt class="col-sm-3">Full Name</dt>
                <dd class="col-sm-9">{{ $student->first_name }} {{ $student->last_name }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $student->email }}</dd>
                <dt class="col-sm-3">Programme</dt>
                <dd class="col-sm-9">{{ $student->programme ?? 'Not specified' }}</dd>
                <dt class="col-sm-3">Year of Study</dt>
                <dd class="col-sm-9">{{ $student->year_of_study ?? 'Not specified' }}</dd>
            </dl>
        </div>
    </div>
@endsection
