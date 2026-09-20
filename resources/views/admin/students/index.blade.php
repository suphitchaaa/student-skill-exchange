@extends('layouts.app')

@section('title', 'จัดการนักศึกษา')
@section('page-title', 'จัดการนักศึกษา')

@section('content')
    <div class="content-heading d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
            <h1 class="mb-1">จัดการนักศึกษา</h1>
            <p class="text-secondary mb-0">ค้นหา ดูข้อมูล และจัดการสถานะบัญชีนักศึกษา</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.students.index') }}" class="campus-card bg-white admin-filter-panel">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="student-search" class="form-label">ค้นหา</label>
                <input id="student-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="ชื่อ รหัสนักศึกษา หรืออีเมล">
            </div>
            <div class="col-12 col-sm-4 col-lg-2">
                <label for="student-status" class="form-label">สถานะ</label>
                <select id="student-status" name="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="active" @selected(request('status') === 'active')>ใช้งานอยู่</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>ถูกระงับ</option>
                </select>
            </div>
            <div class="col-12 col-sm-4 col-lg-2">
                <label for="student-faculty" class="form-label">คณะ</label>
                <select id="student-faculty" name="faculty" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach ($faculties as $faculty)
                        <option value="{{ $faculty }}" @selected(request('faculty') === $faculty)>{{ $faculty }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-4 col-lg-2">
                <label for="student-year" class="form-label">ชั้นปี</label>
                <select id="student-year" name="year_level" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @for ($year = 1; $year <= 8; $year++)
                        <option value="{{ $year }}" @selected((string) request('year_level') === (string) $year)>ปี {{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>ค้นหา</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.students.index') }}" aria-label="ล้างตัวกรอง"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i></a>
            </div>
        </div>
    </form>

    <div class="campus-card bg-white admin-table-panel">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr><th>นักศึกษา</th><th>รหัส</th><th>คณะ / ชั้นปี</th><th>สถานะ</th><th class="text-end">การจัดการ</th></tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td><a href="{{ route('admin.students.show', $student) }}" class="fw-semibold text-decoration-none">{{ $student->name }}</a><div class="small text-secondary">{{ $student->email }}</div></td>
                            <td>{{ $student->student_code }}</td>
                            <td>{{ $student->studentProfile?->faculty ?? 'ไม่ระบุ' }}<div class="small text-secondary">{{ $student->studentProfile?->year_level ? 'ปี '.$student->studentProfile->year_level : 'ไม่ระบุชั้นปี' }}</div></td>
                            <td><span class="badge {{ $student->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $student->status === 'active' ? 'ใช้งานอยู่' : 'ถูกระงับ' }}</span></td>
                            <td class="text-end"><a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-outline-primary">ดูข้อมูล</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">ไม่พบนักศึกษาตามเงื่อนไข</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->hasPages())
            <div class="mt-3">{{ $students->links() }}</div>
        @endif
    </div>
@endsection
