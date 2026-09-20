@extends('layouts.auth')

@section('title', 'สมัครสมาชิก')
@section('auth-variant', 'register')
@section('hero-title-first', 'เริ่มต้นแบ่งปัน')
@section('hero-title-second', 'สร้างเครือข่ายการเรียนรู้')
@section('hero-lead', 'สร้างบัญชีเพื่อแบ่งปันทักษะ ค้นพบเพื่อนที่มีความสนใจเดียวกัน และเรียนรู้ไปด้วยกันในชุมชนมหาวิทยาลัย')
@section('feature-one')เชื่อมต่อ<br>กับเพื่อนใหม่@endsection
@section('feature-two')แบ่งปัน<br>ความรู้และทักษะ@endsection
@section('feature-three')เติบโตไปด้วยกัน<br>ในรั้วมหาวิทยาลัย@endsection

@section('content')
    <p class="auth-form-kicker">การสมัครสมาชิก</p>
    <h1 class="auth-form-title">สมัครสมาชิก</h1>
    <p class="auth-form-lead">สร้างบัญชีเพื่อเริ่มใช้งานระบบแลกเปลี่ยนทักษะ</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="auth-field">
            <label class="form-label" for="name">ชื่อ-นามสกุล</label>
            <div class="auth-input"><i class="bi bi-person" aria-hidden="true"></i><input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="กรอกชื่อ-นามสกุลของคุณ" autocomplete="name" required autofocus></div>
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-field">
            <label class="form-label" for="student_code">รหัสนักศึกษา</label>
            <div class="auth-input"><i class="bi bi-person-vcard" aria-hidden="true"></i><input class="form-control @error('student_code') is-invalid @enderror" id="student_code" name="student_code" type="text" value="{{ old('student_code') }}" placeholder="กรอกรหัสนักศึกษา" required></div>
            @error('student_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-field">
            <label class="form-label" for="email">อีเมล</label>
            <div class="auth-input"><i class="bi bi-envelope" aria-hidden="true"></i><input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="example@student.ac.th" autocomplete="email" required></div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-field">
            <label class="form-label" for="password">รหัสผ่าน</label>
            <div class="auth-input"><i class="bi bi-lock" aria-hidden="true"></i><input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="กรอกรหัสผ่านของคุณ" autocomplete="new-password" required></div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="auth-field">
            <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่าน</label>
            <div class="auth-input"><i class="bi bi-lock" aria-hidden="true"></i><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="ยืนยันรหัสผ่านของคุณ" autocomplete="new-password" required></div>
        </div>
        <button class="btn btn-primary auth-submit w-100" type="submit">สมัครสมาชิก <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>

    <div class="auth-form-divider"><span>หรือ</span></div>
    <p class="auth-form-switch text-center mb-0">มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
@endsection
