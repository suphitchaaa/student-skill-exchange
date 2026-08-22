@extends('layouts.app')

@section('title', 'จัดการทักษะ')
@section('page-title', 'จัดการทักษะ')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div><h1 class="h4 mb-1">จัดการทักษะส่วนกลาง</h1><p class="text-secondary mb-0">เพิ่ม แก้ไข และปิดการใช้งานทักษะของระบบ</p></div>
        <a class="btn btn-primary" href="{{ route('admin.skills.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>เพิ่มทักษะ</a>
    </div>
    <form method="GET" class="campus-card bg-white p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-lg-4"><label class="form-label" for="skill-search">ค้นหาชื่อทักษะ</label><input id="skill-search" name="q" value="{{ request('q') }}" class="form-control"></div>
            <div class="col-12 col-sm-4 col-lg-3"><label class="form-label" for="skill-category">หมวดหมู่</label><select id="skill-category" name="category" class="form-select"><option value="">ทั้งหมด</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select></div>
            <div class="col-12 col-sm-4 col-lg-3"><label class="form-label" for="skill-status">สถานะ</label><select id="skill-status" name="status" class="form-select"><option value="">ทั้งหมด</option><option value="active" @selected(request('status') === 'active')>ใช้งานอยู่</option><option value="inactive" @selected(request('status') === 'inactive')>ปิดใช้งาน</option><option value="deleted" @selected(request('status') === 'deleted')>ลบแบบ Soft Delete</option></select></div>
            <div class="col-12 col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search me-1" aria-hidden="true"></i>ค้นหา</button><a class="btn btn-outline-secondary" href="{{ route('admin.skills.index') }}" aria-label="ล้างตัวกรอง"><i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i></a></div>
        </div>
    </form>
    <div class="campus-card bg-white p-3"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>ชื่อทักษะ</th><th>หมวดหมู่</th><th>สถานะ</th><th class="text-end">การจัดการ</th></tr></thead><tbody>
        @forelse($skills as $skill)<tr><td>{{ $skill->name }}</td><td>{{ $skill->category }}</td><td>@if($skill->trashed())<span class="badge text-bg-secondary">Soft Delete</span>@elseif($skill->is_active)<span class="badge text-bg-success">ใช้งานอยู่</span>@else<span class="badge text-bg-warning">ปิดใช้งาน</span>@endif</td><td class="text-end">@if(!$skill->trashed())<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.skills.edit', $skill) }}">แก้ไข</a><form class="d-inline" method="POST" action="{{ route('admin.skills.destroy', $skill) }}" onsubmit="return confirm('ยืนยันการปิดการใช้งานทักษะนี้หรือไม่')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">ปิดการใช้งาน</button></form>@endif</td></tr>@empty<tr><td colspan="4" class="text-center text-secondary py-4">ไม่พบทักษะตามเงื่อนไข</td></tr>@endforelse
    </tbody></table></div>@if($skills->hasPages())<div class="mt-3">{{ $skills->links() }}</div>@endif</div>
@endsection
