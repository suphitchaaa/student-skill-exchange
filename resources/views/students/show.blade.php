@extends('layouts.app')

@section('title', 'โปรไฟล์นักศึกษา')
@section('page-title', 'โปรไฟล์นักศึกษา')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">โปรไฟล์นักศึกษา</h1>
            <p class="text-secondary mb-0">ข้อมูลที่นักศึกษาเลือกแสดงในระบบ</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('students.index') }}">กลับไปค้นหา</a>
    </div>

    @if ($student->id !== auth()->id())
        <div class="mb-4">
            <a class="btn btn-primary" href="{{ route('exchange-requests.create', $student) }}"><i class="bi bi-send me-1" aria-hidden="true"></i>เริ่มคำขอแลกเปลี่ยน</a>
        </div>
    @endif

    <section class="campus-card bg-white p-4 mb-4">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-4">
            @if ($profileImageUrl)
                <img class="public-profile-image" src="{{ $profileImageUrl }}" alt="รูปโปรไฟล์ของ {{ $student->name }}">
            @else
                <div class="public-profile-image-placeholder" aria-hidden="true"><i class="bi bi-person"></i></div>
            @endif
            <div>
                <h2 class="h4 mb-2">{{ $student->name }}</h2>
                <p class="mb-1">{{ $student->studentProfile?->faculty ?: 'ยังไม่ระบุคณะ' }}</p>
                @if ($student->studentProfile?->major)
                    <p class="text-secondary mb-1">{{ $student->studentProfile->major }}</p>
                @endif
                @if ($student->studentProfile?->year_level)
                    <p class="text-secondary mb-0">ชั้นปี {{ $student->studentProfile->year_level }}</p>
                @endif
            </div>
        </div>
        @if ($student->studentProfile?->bio)
            <hr>
            <h3 class="h6">แนะนำตัว</h3>
            <p class="mb-0">{{ $student->studentProfile->bio }}</p>
        @endif
    </section>

    <div class="row g-4">
        @foreach (['offered' => 'ทักษะที่สอนได้', 'wanted' => 'ทักษะที่ต้องการเรียน'] as $type => $title)
            <div class="col-12 col-md-6">
                <section class="campus-card bg-white p-4 h-100">
                    <h2 class="h5 mb-3">{{ $title }}</h2>
                    @forelse ($student->userSkills->where('skill_type', $type) as $userSkill)
                        <div class="border-bottom py-3 last-border-0">
                            <h3 class="h6 mb-1">{{ $userSkill->skill?->name }}</h3>
                            @if ($userSkill->description)
                                <p class="small text-secondary mb-0">{{ $userSkill->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary mb-0">ยังไม่มีรายการ</p>
                    @endforelse
                </section>
            </div>
        @endforeach
    </div>
@endsection
