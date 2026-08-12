@extends('layouts.auth')

@section('title', 'บัญชีถูกระงับ')

@section('content')
    <div class="text-center">
        <i class="bi bi-person-x fs-1 text-danger" aria-hidden="true"></i>
        <h1 class="h3 mt-3 mb-2">บัญชีของคุณถูกระงับการใช้งาน</h1>
        <p class="text-secondary mb-4">ไม่สามารถเข้าใช้งานพื้นที่ระบบได้ในขณะนี้ กรุณาติดต่อผู้ดูแลระบบเพื่อขอข้อมูลเพิ่มเติม</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">ออกจากระบบ</button>
        </form>
    </div>
@endsection
