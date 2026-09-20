<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | ระบบแลกเปลี่ยนทักษะ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // คืนค่าการแสดงแถบเมนูก่อนวาดหน้า เพื่อลดการกระโดดของพื้นที่เนื้อหา
        try {
            if (localStorage.getItem('studentSkillExchange.sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('app-sidebar-collapsed');
            }
        } catch (error) {
            // ยังใช้งานเมนูได้ตามปกติเมื่อเบราว์เซอร์ปิด localStorage
        }
    </script>
</head>
<body>
    <div class="container-fluid px-0 app-shell">
        <div class="row g-0">
            <aside class="col-12 col-md-3 col-lg-2 app-sidebar p-3 p-lg-4">
                <div class="app-sidebar-header">
                    <a class="app-sidebar-brand d-flex align-items-center gap-2 text-decoration-none fw-semibold" href="{{ $dashboardRoute }}" title="ระบบแลกเปลี่ยนทักษะ">
                        <i class="bi bi-people-fill" aria-hidden="true"></i>
                        <span class="app-sidebar-brand-name">ระบบแลกเปลี่ยนทักษะ</span>
                    </a>
                </div>
                <p class="sidebar-section-label mb-2">เมนูหลัก</p>
                <nav class="nav flex-column" id="app-sidebar-navigation" aria-label="เมนูหลัก">
                    <a class="nav-link {{ request()->routeIs($dashboardRouteName) ? 'active' : '' }}" href="{{ $dashboardRoute }}" title="ภาพรวม">
                        <i class="bi bi-grid-1x2 me-2" aria-hidden="true"></i><span class="sidebar-link-label">ภาพรวม</span>
                    </a>
                    @if (auth()->user()->role === 'student')
                        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}" title="ค้นหาทักษะ">
                            <i class="bi bi-search me-2" aria-hidden="true"></i><span class="sidebar-link-label">ค้นหาทักษะ</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('user-skills.*') ? 'active' : '' }}" href="{{ route('user-skills.index') }}" title="ทักษะของฉัน">
                            <i class="bi bi-journal-check me-2" aria-hidden="true"></i><span class="sidebar-link-label">ทักษะของฉัน</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('exchange-requests.*') ? 'active' : '' }}" href="{{ route('exchange-requests.index') }}" title="คำขอแลกเปลี่ยน">
                            <i class="bi bi-arrow-left-right me-2" aria-hidden="true"></i><span class="sidebar-link-label">คำขอแลกเปลี่ยน</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}" title="โปรไฟล์ของฉัน">
                            <i class="bi bi-person-vcard me-2" aria-hidden="true"></i><span class="sidebar-link-label">โปรไฟล์ของฉัน</span>
                        </a>
                    @elseif (auth()->user()->role === 'admin')
                        <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}" title="จัดการนักศึกษา">
                            <i class="bi bi-person-lines-fill me-2" aria-hidden="true"></i><span class="sidebar-link-label">จัดการนักศึกษา</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.skills.*') ? 'active' : '' }}" href="{{ route('admin.skills.index') }}" title="จัดการทักษะ">
                            <i class="bi bi-tags me-2" aria-hidden="true"></i><span class="sidebar-link-label">จัดการทักษะ</span>
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.exchange-requests.*') ? 'active' : '' }}" href="{{ route('admin.exchange-requests.index') }}" title="ตรวจสอบคำขอ">
                            <i class="bi bi-list-check me-2" aria-hidden="true"></i><span class="sidebar-link-label">ตรวจสอบคำขอ</span>
                        </a>
                    @endif
                </nav>
                <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-wrap">
                    @csrf
                    <button type="submit" class="sidebar-logout border-0 w-100 text-start" title="ออกจากระบบ">
                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i><span class="sidebar-link-label">ออกจากระบบ</span>
                    </button>
                </form>
            </aside>
            <button class="app-sidebar-toggle" type="button" aria-controls="app-sidebar-navigation" aria-expanded="true" aria-label="ยุบแถบเมนู" title="ยุบแถบเมนู">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="app-content col-12 col-md-9 col-lg-10 min-vh-100">
                <header class="app-topbar px-3 px-md-4 py-2 d-flex align-items-center justify-content-between">
                    <div>
                        <p class="app-topbar-title mb-0">@yield('page-title')</p>
                        <small class="app-topbar-role text-secondary">{{ $roleLabel }}</small>
                    </div>
                    <span class="app-topbar-identity small"><i class="bi bi-person-circle" aria-hidden="true"></i><span class="app-topbar-identity-name text-truncate">{{ auth()->user()->name }}</span></span>
                </header>
                <main class="p-3 p-md-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    <script>
        const sidebarToggle = document.querySelector('.app-sidebar-toggle');
        const syncSidebarToggle = () => {
            const collapsed = document.documentElement.classList.contains('app-sidebar-collapsed');
            const label = collapsed ? 'ขยายแถบเมนู' : 'ยุบแถบเมนู';

            sidebarToggle.setAttribute('aria-expanded', String(!collapsed));
            sidebarToggle.setAttribute('aria-label', label);
            sidebarToggle.title = label;
            sidebarToggle.querySelector('.bi').className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        };

        syncSidebarToggle();
        sidebarToggle.addEventListener('click', () => {
            const collapsed = document.documentElement.classList.toggle('app-sidebar-collapsed');
            try {
                localStorage.setItem('studentSkillExchange.sidebarCollapsed', String(collapsed));
            } catch (error) {
                // ปุ่มยุบขยายยังทำงานได้แม้บันทึกค่าไม่ได้
            }
            syncSidebarToggle();
        });
    </script>
</body>
</html>
