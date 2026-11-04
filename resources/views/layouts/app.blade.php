<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Campus Connection')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
    @endphp

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Campus Connection</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    @if($user->can('students.view'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">Students</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">Courses</a></li>
                    @if($user->can('registrations.view'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('registrations.index') }}">Registrations</a></li>
                    @endif
                    @if($user->can('users.view'))
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">User Access</a></li>
                    @endif
                </ul>
                <span class="navbar-text text-white-50">Role: {{ ucfirst($role) }}</span>
                <form method="POST" action="{{ route('logout') }}" class="ms-3">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Sign out</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-light border-top mt-5">
        <div class="container py-3 text-center text-muted">
            &copy; {{ date('Y') }} Campus Connection
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
