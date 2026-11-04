@extends('layouts.app')

@section('title', 'User Access')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>User Access</h1>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Update access</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roles->pluck('name')->map(fn ($role) => ucfirst($role))->join(', ') ?: 'Unassigned' }}</td>
                            <td>
                                <form action="{{ route('admin.users.role', $user) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm" aria-label="Role for {{ $user->name }}">
                                        <option value="" disabled @selected($user->roles->isEmpty())>Unassigned</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection