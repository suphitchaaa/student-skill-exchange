<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | ระบบแลกเปลี่ยนทักษะ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-page--@yield('auth-variant', 'login')">
    <header class="auth-masthead">
        <div class="auth-masthead-inner">
            <a class="auth-brand text-decoration-none" href="{{ route('home') }}">
                <i class="bi bi-mortarboard-fill" aria-hidden="true"></i>
                <span>ระบบแลกเปลี่ยนทักษะ</span>
            </a>
            <p class="auth-masthead-motto">เรียนรู้&nbsp; แบ่งปัน&nbsp; เติบโตไปด้วยกัน</p>
            <span class="auth-masthead-note">พื้นที่การเรียนรู้ในมหาวิทยาลัย</span>
        </div>
    </header>
    <main class="auth-container">
        <div class="auth-shell">
            <section class="auth-context" aria-label="เกี่ยวกับระบบแลกเปลี่ยนทักษะ">
                <div class="auth-context-copy">
                    <p class="auth-kicker">STUDENT SKILL EXCHANGE</p>
                    <h2 class="auth-context-title">@yield('hero-title-first')<br><span>@yield('hero-title-second')</span></h2>
                    <p class="auth-context-lead">@yield('hero-lead')</p>
                    <div class="auth-features" aria-label="ประโยชน์ของระบบ">
                        <div class="auth-feature"><span class="auth-feature-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span><span>@yield('feature-one')</span></div>
                        <div class="auth-feature"><span class="auth-feature-icon"><i class="bi bi-lightbulb" aria-hidden="true"></i></span><span>@yield('feature-two')</span></div>
                        <div class="auth-feature"><span class="auth-feature-icon"><i class="bi bi-bar-chart-fill" aria-hidden="true"></i></span><span>@yield('feature-three')</span></div>
                    </div>
                </div>
                <p class="auth-context-footer">SAME UNIVERSITY <span>·</span> BRIGHTER PEOPLE</p>
            </section>
            <section class="auth-form-panel">
                <div class="auth-form-inner">
                    @yield('content')
                </div>
            </section>
        </div>
    </main>
</body>
</html>
