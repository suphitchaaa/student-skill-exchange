@extends('layouts.auth')

@section('title', 'เข้าสู่ระบบ')

@section('content')
    <h1 class="h3 mb-2">เข้าสู่ระบบ</h1>
    <p class="text-secondary mb-4">ใช้บัญชีที่ลงทะเบียนไว้เพื่อเข้าใช้งาน</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label" for="email">อีเมล</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">รหัสผ่าน</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
            <label class="form-check-label" for="remember">จดจำการเข้าสู่ระบบ</label>
        </div>
        <button class="btn btn-primary w-100" type="submit">เข้าสู่ระบบ</button>
    </form>

    <p class="text-center text-secondary small mt-4 mb-0">ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a></p>
@endsection
