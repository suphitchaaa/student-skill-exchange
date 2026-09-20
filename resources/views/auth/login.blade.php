@extends('layouts.auth')

@section('title', 'เข้าสู่ระบบ')
@section('auth-variant', 'login')
@section('hero-title-first', 'แบ่งปันทักษะ')
@section('hero-title-second', 'เรียนรู้ร่วมกัน')
@section('hero-lead', 'ค้นหาทักษะที่น่าสนใจ และแบ่งปันทักษะที่คุณมี กับเพื่อน ๆ ในมหาวิทยาลัย สร้างโอกาสใหม่ ๆ ไปด้วยกัน')
@section('feature-one')เรียนรู้<br>จากเพื่อน@endsection
@section('feature-two')แบ่งปัน<br>สิ่งที่คุณถนัด@endsection
@section('feature-three')สร้างสังคม<br>แห่งการเติบโต@endsection

@section('content')
    <p class="auth-form-kicker">การเข้าสู่ระบบ</p>
    <h1 class="auth-form-title">เข้าสู่ระบบ</h1>
    <p class="auth-form-lead">ใช้บัญชีที่ลงทะเบียนไว้เพื่อเข้าใช้งาน</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="auth-field">
            <label class="form-label" for="email">อีเมล</label>
            <div class="auth-input">
                <i class="bi bi-envelope" aria-hidden="true"></i>
                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="example@student.ac.th" autocomplete="email" required autofocus>
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-field">
            <label class="form-label" for="password">รหัสผ่าน</label>
            <div class="auth-input">
                <i class="bi bi-lock" aria-hidden="true"></i>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="กรอกรหัสผ่านของคุณ" autocomplete="current-password" required>
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-form-options">
            <div class="form-check">
                <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                <label class="form-check-label" for="remember">จดจำการเข้าสู่ระบบ</label>
            </div>
        </div>
        <button class="btn btn-primary auth-submit w-100" type="submit">เข้าสู่ระบบ <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-form-divider"><span>หรือ</span></div>
    <p class="auth-form-switch text-center mb-0">ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a></p>
@endsection
