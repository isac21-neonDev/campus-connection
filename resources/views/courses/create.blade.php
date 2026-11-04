@extends('layouts.app')

@section('title', 'Add Course')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Add Course</h1>
        <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Credit Units</label>
                        <input type="number" name="credit_units" min="1" max="12" class="form-control" value="{{ old('credit_units') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Save Course</button>
            </form>
        </div>
    </div>
@endsection
