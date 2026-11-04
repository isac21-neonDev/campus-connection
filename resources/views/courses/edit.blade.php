@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Course</h1>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $course->code) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Credit Units</label>
                        <input type="number" name="credit_units" min="1" max="12" class="form-control" value="{{ old('credit_units', $course->credit_units) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $course->title) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $course->description) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update Course</button>
            </form>
        </div>
    </div>
@endsection
