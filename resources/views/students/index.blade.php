@extends('layouts.app')

@section('title', 'ค้นหาทักษะ')
@section('page-title', 'ค้นหาทักษะ')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">ค้นหานักศึกษา</h1>
        <p class="text-secondary mb-0">ค้นหานักศึกษาที่มีทักษะและความสนใจตรงกับคุณ</p>
    </div>

    <section class="campus-card bg-white p-4 mb-4">
        <form action="{{ route('students.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="search">ชื่อนักศึกษา</label>
                    <input class="form-control" id="search" name="search" value="{{ $search }}" placeholder="ค้นหาด้วยชื่อ">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label" for="skill_id">ทักษะ</label>
                    <select class="form-select" id="skill_id" name="skill_id">
                        <option value="">ทุกทักษะ</option>
                        @foreach ($availableSkills as $skill)
                            <option value="{{ $skill->id }}" @selected($skillId === $skill->id)>{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="faculty">คณะ</label>
                    <select class="form-select" id="faculty" name="faculty">
                        <option value="">ทุกคณะ</option>
                        @foreach ($faculties as $facultyOption)
                            <option value="{{ $facultyOption }}" @selected($faculty === $facultyOption)>{{ $facultyOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label" for="year_level">ชั้นปี</label>
                    <select class="form-select" id="year_level" name="year_level">
                        <option value="">ทุกชั้นปี</option>
                        @for ($year = 1; $year <= 8; $year++)
                            <option value="{{ $year }}" @selected($yearLevel === $year)>ปี {{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-lg-1 d-grid">
                    <button class="btn btn-primary" type="submit">ค้นหา</button>
                </div>
            </div>
        </form>
    </section>

    @if ($students->isEmpty())
        <section class="campus-card bg-white p-5 text-center">
            <i class="bi bi-person-x fs-2 text-secondary" aria-hidden="true"></i>
            <h2 class="h5 mt-3">ไม่พบนักศึกษาตามเงื่อนไข</h2>
            <p class="text-secondary">ลองเปลี่ยนคำค้นหาหรือรีเซ็ตตัวกรองเพื่อดูนักศึกษาทั้งหมด</p>
            <a class="btn btn-outline-primary" href="{{ route('students.index') }}">ล้างตัวกรอง</a>
        </section>
    @else
        <div class="row g-3">
            @foreach ($students as $student)
                <div class="col-12 col-md-6 col-xl-4">
                    <article class="campus-card bg-white h-100 p-4 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            @if ($student->studentProfile?->profile_image)
                                <img class="student-avatar" src="{{ asset('storage/'.ltrim($student->studentProfile->profile_image, '/')) }}" alt="รูปโปรไฟล์ของ {{ $student->name }}">
                            @else
                                <div class="student-avatar-placeholder" aria-hidden="true"><i class="bi bi-person"></i></div>
                            @endif
                            <div>
                                <h2 class="h6 mb-1">{{ $student->name }}</h2>
                                <p class="small text-secondary mb-0">{{ $student->studentProfile?->faculty ?: 'ยังไม่ระบุคณะ' }}</p>
                                @if ($student->studentProfile?->year_level)
                                    <p class="small text-secondary mb-0">ชั้นปี {{ $student->studentProfile->year_level }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            @forelse ($student->userSkills->take(4) as $userSkill)
                                <span class="badge text-bg-light border me-1 mb-1">{{ $userSkill->skill?->name }}</span>
                            @empty
                                <span class="small text-secondary">ยังไม่มีทักษะที่ระบุ</span>
                            @endforelse
                            @if ($student->userSkills->count() > 4)
                                <span class="small text-secondary">และอีก {{ $student->userSkills->count() - 4 }} ทักษะ</span>
                            @endif
                        </div>
                        <a class="btn btn-outline-primary mt-auto" href="{{ route('students.show', $student) }}">ดูโปรไฟล์</a>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $students->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
    @endif
@endsection
