<!DOCTYPE html>
<html lang="en" data-bs-theme="light" id="appTheme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HMS - @yield('title', 'HMS')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            transition: background-color 0.3s, color 0.3s;
        }
    </style>

</head>

<body class="bg-body text-body">

    <!-- Loader -->
    <div id="globalLoader"
        class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"
        style="z-index: 1055;">
        <div class="spinner-border text-light"></div>
    </div>

    <!-- Toast -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>

    <!-- Navbar -->
    @if(!($hideHeader ?? false))
    <nav class="navbar navbar-expand-lg bg-body border-bottom">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand fw-bold" href="/dashboard">HMS</a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left side links -->
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/roles">
                                Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/users">
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/doctors">
                                Doctors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/patients">
                                Patients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/appointments">
                                Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pharmacy">
                                Pharmacy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/reports">
                                Reports
                            </a>
                        </li>
                    @endauth

                    @guest
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                                Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('register') ? 'active' : '' }}" href="/register">
                                Register
                            </a>
                        </li>
                    @endguest
                </ul>

                <!-- Right side (User) -->
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->username }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="/profile">Profile</a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <form onsubmit="logoutUser(event)">
                                        @csrf
                                        <button class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                    <button class="btn btn-secondary" onclick="toggleTheme()" id="themeBtn">
                        Dark Mode
                    </button>
                </ul>
            </div>
        </div>
    </nav>
    @endif

    <!-- Content -->
    <div class="container-fluid py-4">
        <div class="bg-body">
            <div>
                <h4 class="mb-4 text-center">@yield('page-title')</h4>
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function showLoader() {
            $('#globalLoader').removeClass('d-none');
        }

        function hideLoader() {
            $('#globalLoader').addClass('d-none');
        }

        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast align-items-center text-bg-${type === 'error' ? 'danger' : type} border-0 show mb-2">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="$(this).closest('.toast').remove()"></button>
                    </div>
                </div>
            `);

            $('#toastContainer').append(toast);

            setTimeout(() => {
                toast.fadeOut(500, function() {
                    $(this).remove();
                });
            }, 700);
        }

        function redirect(location = "/", timeOut = 500) {
            setTimeout(() => {
                window.location.href = location;
            }, timeOut);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleTheme() {
            const html = document.getElementById('appTheme');
            const btn = document.getElementById('themeBtn');

            const current = html.getAttribute('data-bs-theme');
            const newTheme = current === 'light' ? 'dark' : 'light';

            html.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            btn.innerText = newTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }

        $(document).ready(function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const btn = document.getElementById('themeBtn');

            document.getElementById('appTheme').setAttribute('data-bs-theme', savedTheme);

            btn.innerText = savedTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
        });

        function logoutUser(e) {
            e.preventDefault();
            $.ajax({
                url: "/logout",
                method: "POST",

                beforeSend: function() {
                    showLoader();
                },

                success: function(response) {
                    console.log(response);
                    showToast(response?.message);
                    redirect("/");
                },

                error: function(error) {
                    console.log(error?.responseJSON);
                    showToast(error?.responseJSON?.message, 'error');
                },

                complete: function() {
                    hideLoader();
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
