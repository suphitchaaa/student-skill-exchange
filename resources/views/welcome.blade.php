<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ระบบแลกเปลี่ยนทักษะ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <section class="campus-card bg-white p-4 p-md-5">
                    <div class="d-flex align-items-center gap-2 campus-brand mb-4">
                        <i class="bi bi-people-fill fs-4" aria-hidden="true"></i>
                        <span>ระบบแลกเปลี่ยนทักษะระหว่างนักศึกษา</span>
                    </div>
                    <h1 class="h3 mb-3">เรียนรู้และแบ่งปันทักษะร่วมกันในมหาวิทยาลัย</h1>
                    <p class="text-secondary mb-4">เข้าสู่ระบบเพื่อดูพื้นที่ของคุณ หรือสมัครบัญชีนักศึกษาใหม่</p>
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            <a class="btn btn-primary" href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('student.dashboard') }}">ไปยังภาพรวม</a>
                        @else
                            <a class="btn btn-primary" href="{{ route('login') }}">เข้าสู่ระบบ</a>
                            <a class="btn btn-outline-primary" href="{{ route('register') }}">สมัครสมาชิก</a>
                        @endauth
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
