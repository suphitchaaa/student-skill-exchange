@extends('layouts.auth')

@section('title', 'สมัครสมาชิก')

@section('content')
    <h1 class="h3 mb-2">สมัครสมาชิก</h1>
    <p class="text-secondary mb-4">สำหรับนักศึกษาเท่านั้น</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="name">ชื่อ-นามสกุล</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="student_code">รหัสนักศึกษา</label>
            <input class="form-control @error('student_code') is-invalid @enderror" id="student_code" name="student_code" type="text" value="{{ old('student_code') }}" required>
            @error('student_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="email">อีเมล</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">รหัสผ่าน</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่าน</label>
            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">สมัครสมาชิก</button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">มีบัญชีอยู่แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
@endsection
