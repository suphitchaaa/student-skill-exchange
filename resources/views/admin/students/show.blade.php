@extends('layouts.app')

@section('title', 'ข้อมูลนักศึกษา')
@section('page-title', 'ข้อมูลนักศึกษา')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i>กลับไปยังรายชื่อนักศึกษา</a>
            <h1 class="h4 mt-2 mb-1">{{ $student->name }}</h1>
            <p class="text-secondary mb-0">ข้อมูลบัญชีและโปรไฟล์นักศึกษา</p>
        </div>
        <span class="badge fs-6 {{ $student->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $student->status === 'active' ? 'ใช้งานอยู่' : 'ถูกระงับ' }}</span>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <section class="campus-card bg-white p-3 h-100">
                <h2 class="h6 mb-3">ข้อมูลพื้นฐาน</h2>
                <dl class="row profile-details mb-0">
                    <dt class="col-sm-4">ชื่อ</dt><dd class="col-sm-8">{{ $student->name }}</dd>
                    <dt class="col-sm-4">รหัสนักศึกษา</dt><dd class="col-sm-8">{{ $student->student_code }}</dd>
                    <dt class="col-sm-4">อีเมล</dt><dd class="col-sm-8">{{ $student->email }}</dd>
                    <dt class="col-sm-4">คณะ</dt><dd class="col-sm-8">{{ $student->studentProfile?->faculty ?? 'ไม่ระบุ' }}</dd>
                    <dt class="col-sm-4">สาขา</dt><dd class="col-sm-8">{{ $student->studentProfile?->major ?? 'ไม่ระบุ' }}</dd>
                    <dt class="col-sm-4">ชั้นปี</dt><dd class="col-sm-8">{{ $student->studentProfile?->year_level ? 'ปี '.$student->studentProfile->year_level : 'ไม่ระบุ' }}</dd>
                </dl>
            </section>
        </div>
        <div class="col-12 col-lg-5">
            <section class="campus-card bg-white p-3 h-100">
                <h2 class="h6 mb-3">จัดการสถานะบัญชี</h2>
                <p class="text-secondary small">การเปลี่ยนสถานะมีผลกับการเข้าใช้งานของนักศึกษาทันที</p>
                @if ($student->status === 'active')
                    <form method="POST" action="{{ route('admin.students.suspend', $student) }}" onsubmit="return confirm('ยืนยันการระงับบัญชีนักศึกษาคนนี้หรือไม่?')">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-person-dash me-1" aria-hidden="true"></i>ระงับบัญชี</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.students.activate', $student) }}" onsubmit="return confirm('ยืนยันการเปิดใช้งานบัญชีนักศึกษาคนนี้หรือไม่?')">
                        @csrf @method('PATCH')
                        <button class="btn btn-success" type="submit"><i class="bi bi-person-check me-1" aria-hidden="true"></i>เปิดใช้งานบัญชี</button>
                    </form>
                @endif
            </section>
        </div>
        <div class="col-12">
            <section class="campus-card bg-white p-3">
                <h2 class="h6 mb-3">ทักษะของนักศึกษา</h2>
                @forelse ($student->userSkills as $userSkill)
                    <span class="badge text-bg-light border me-1 mb-1">{{ $userSkill->skill->name }} · {{ $userSkill->skill_type === 'offered' ? 'เสนอ' : 'ต้องการ' }}</span>
                @empty
                    <p class="text-secondary mb-0">ยังไม่มีทักษะ</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
