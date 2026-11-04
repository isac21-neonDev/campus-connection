@extends('layouts.app')

@section('title', 'Course Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Course Details</h1>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Code</dt>
                <dd class="col-sm-9">{{ $course->code }}</dd>
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $course->title }}</dd>
                <dt class="col-sm-3">Credits</dt>
                <dd class="col-sm-9">{{ $course->credit_units ?? 'Not specified' }}</dd>
                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $course->description ?? 'No description provided.' }}</dd>
            </dl>
        </div>
    </div>
@endsection
