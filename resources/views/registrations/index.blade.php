@extends('layouts.app')

@section('title', 'Registrations')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Registrations</h1>
        @if($canManageRegistrations)
            <a href="{{ route('registrations.create') }}" class="btn btn-primary">Add Registration</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($registrations->isEmpty())
                <p class="text-muted mb-0">No registrations found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Registered On</th>
                                @if($canManageRegistrations)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td>{{ $registration->student->first_name ?? '' }} {{ $registration->student->last_name ?? '' }}</td>
                                    <td>{{ $registration->course->title ?? 'Unknown course' }}</td>
                                    <td>{{ $registration->created_at->format('d M Y') }}</td>
                                    @if($canManageRegistrations)
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('registrations.edit', $registration) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('registrations.destroy', $registration) }}" method="POST" onsubmit="return confirm('Delete this registration?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
