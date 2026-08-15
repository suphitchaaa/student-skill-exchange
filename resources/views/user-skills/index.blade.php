@extends('layouts.app')

@section('title', 'ทักษะของฉัน')
@section('page-title', 'ทักษะของฉัน')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">จัดการทักษะของฉัน</h1>
            <p class="text-secondary mb-0">ระบุทักษะที่คุณสอนได้และทักษะที่ต้องการเรียนรู้</p>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeSkillType === 'offered' ? 'active' : '' }}" href="{{ route('user-skills.index', ['type' => 'offered']) }}">
                ทักษะที่สอนได้
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSkillType === 'wanted' ? 'active' : '' }}" href="{{ route('user-skills.index', ['type' => 'wanted']) }}">
                ทักษะที่ต้องการเรียน
            </a>
        </li>
    </ul>

    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <section class="campus-card bg-white p-4">
                <h2 class="h5 mb-3">เพิ่ม{{ $activeSkillType === 'offered' ? 'ทักษะที่สอนได้' : 'ทักษะที่ต้องการเรียน' }}</h2>
                <form action="{{ route('user-skills.store') }}" method="POST">
                    @csrf
                    <input name="skill_type" type="hidden" value="{{ $activeSkillType }}">
                    <div class="mb-3">
                        <label class="form-label" for="skill_id">ทักษะ</label>
                        <select class="form-select @error('skill_id') is-invalid @enderror" id="skill_id" name="skill_id" required>
                            <option value="">เลือกทักษะ</option>
                            @foreach ($availableSkills as $skill)
                                <option value="{{ $skill->id }}" @selected((string) old('skill_id') === (string) $skill->id)>{{ $skill->name }}{{ $skill->category ? ' ('.$skill->category.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('skill_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>เพิ่มทักษะ</button>
                </form>
            </section>
        </div>
        <div class="col-12 col-xl-7">
            <section class="campus-card bg-white p-4">
                <h2 class="h5 mb-3">รายการทักษะ</h2>
                @forelse ($userSkills as $userSkill)
                    <article class="border rounded p-3 mb-3">
                        <form action="{{ route('user-skills.update', $userSkill) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="skill_id_{{ $userSkill->id }}">ทักษะ</label>
                                    <select class="form-select" id="skill_id_{{ $userSkill->id }}" name="skill_id" required>
                                        @foreach ($availableSkills as $skill)
                                            <option value="{{ $skill->id }}" @selected($userSkill->skill_id === $skill->id)>{{ $skill->name }}{{ $skill->category ? ' ('.$skill->category.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label" for="skill_type_{{ $userSkill->id }}">ประเภท</label>
                                    <select class="form-select" id="skill_type_{{ $userSkill->id }}" name="skill_type" required>
                                        <option value="offered" @selected($userSkill->skill_type === 'offered')>ทักษะที่สอนได้</option>
                                        <option value="wanted" @selected($userSkill->skill_type === 'wanted')>ทักษะที่ต้องการเรียน</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description_{{ $userSkill->id }}">รายละเอียดเพิ่มเติม</label>
                                    <textarea class="form-control" id="description_{{ $userSkill->id }}" name="description" rows="2">{{ $userSkill->description }}</textarea>
                                </div>
                                <div class="col-12 d-flex flex-wrap gap-2">
                                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-pencil-square me-1" aria-hidden="true"></i>บันทึกการแก้ไข</button>
                                </div>
                            </div>
                        </form>
                        <form action="{{ route('user-skills.destroy', $userSkill) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash me-1" aria-hidden="true"></i>ลบทักษะ</button>
                        </form>
                    </article>
                @empty
                    <p class="text-secondary mb-0">ยังไม่มีรายการทักษะในหมวดนี้</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
