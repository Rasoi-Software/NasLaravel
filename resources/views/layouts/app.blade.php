<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'MyApp') }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
body {
    background-color: #f8fafc;
}
</style>

@stack('styles')
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
<div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">
        {{ config('app.name', 'MyApp') }}
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarContent" aria-controls="navbarContent"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto">
            @auth
                @if(auth()->user()->role == 'user')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.payments') }}">Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.subscriptions') }}">Subscriptions</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="#">{{ auth()->user()->name }}</a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="nav-link btn btn-link text-white" style="text-decoration: none;">Logout</button>
                    </form>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
            @endauth
        </ul>
    </div>
</div>
</nav>

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<main class="py-4">
    @yield('content')
</main>

<footer class="bg-light text-center py-3 mt-auto border-top">
<div class="container">
    <small>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

@stack('scripts')

<script>
$(document).ready(function() {
    $('table').DataTable();
});
    function submitSubscribeForm() {
        const name = document.getElementById('modal-name').value.trim();
        const email = document.getElementById('modal-email').value.trim();

        if (!name || !email) {
            alert('Please fill in both name and email.');
            return;
        }

        document.getElementById('input-name').value = name;
        document.getElementById('input-email').value = email;

        document.getElementById('subscribe-form').submit();
    }

</script>

</body>
</html>
