<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | ระบบแลกเปลี่ยนทักษะ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page d-flex align-items-center py-4">
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <section class="auth-card bg-white p-4 p-md-5">
                    <a class="campus-brand d-inline-flex align-items-center gap-2 mb-4 text-decoration-none" href="{{ route('home') }}">
                        <i class="bi bi-people-fill" aria-hidden="true"></i>
                        <span>ระบบแลกเปลี่ยนทักษะ</span>
                    </a>
                    @yield('content')
                </section>
            </div>
        </div>
    </main>
</body>
</html>
