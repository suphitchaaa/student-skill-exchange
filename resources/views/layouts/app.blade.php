<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | ระบบแลกเปลี่ยนทักษะ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container-fluid px-0">
        <div class="row g-0">
            <aside class="col-12 col-md-3 col-lg-2 app-sidebar p-3">
                <a class="d-flex align-items-center gap-2 mb-4 text-decoration-none text-white fw-semibold" href="{{ $dashboardRoute }}">
                    <i class="bi bi-people-fill" aria-hidden="true"></i>
                    <span>ระบบแลกเปลี่ยนทักษะ</span>
                </a>
                <nav class="nav flex-column gap-1" aria-label="เมนูหลัก">
                    <a class="nav-link {{ request()->routeIs($dashboardRouteName) ? 'active' : '' }}" href="{{ $dashboardRoute }}">
                        <i class="bi bi-grid-1x2 me-2" aria-hidden="true"></i>ภาพรวม
                    </a>
                    @if (auth()->user()->role === 'student')
                        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                            <i class="bi bi-search me-2" aria-hidden="true"></i>ค้นหาทักษะ
                        </a>
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                            <i class="bi bi-person-vcard me-2" aria-hidden="true"></i>โปรไฟล์ของฉัน
                        </a>
                        <a class="nav-link {{ request()->routeIs('user-skills.*') ? 'active' : '' }}" href="{{ route('user-skills.index') }}">
                            <i class="bi bi-journal-check me-2" aria-hidden="true"></i>ทักษะของฉัน
                        </a>
                        <a class="nav-link {{ request()->routeIs('exchange-requests.*') ? 'active' : '' }}" href="{{ route('exchange-requests.index') }}">
                            <i class="bi bi-arrow-left-right me-2" aria-hidden="true"></i>คำขอแลกเปลี่ยน
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>ออกจากระบบ
                        </button>
                    </form>
                </nav>
            </aside>
            <div class="col-12 col-md-9 col-lg-10 min-vh-100">
                <header class="app-topbar px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-0 fw-semibold">@yield('page-title')</p>
                        <small class="text-secondary">{{ $roleLabel }}</small>
                    </div>
                    <span class="text-secondary small"><i class="bi bi-person-circle me-1" aria-hidden="true"></i>{{ auth()->user()->name }}</span>
                </header>
                <main class="p-3 p-md-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>
</html>
