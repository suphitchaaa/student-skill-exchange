@extends('layouts.app')

@section('title', 'ส่งคำขอแลกเปลี่ยน')
@section('page-title', 'ส่งคำขอแลกเปลี่ยน')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">ส่งคำขอแลกเปลี่ยน</h1>
            <p class="text-secondary mb-0">ส่งคำขอถึง {{ $receiver->name }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('students.show', $receiver) }}">กลับไปโปรไฟล์</a>
    </div>

    <form class="campus-card bg-white p-4" action="{{ route('exchange-requests.store', $receiver) }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label" for="sender_user_skill_id">ทักษะที่คุณสอนได้</label>
                <select class="form-select @error('sender_user_skill_id') is-invalid @enderror" id="sender_user_skill_id" name="sender_user_skill_id" required>
                    <option value="">เลือกทักษะของคุณ</option>
                    @foreach ($senderSkills as $userSkill)
                        <option value="{{ $userSkill->id }}" @selected((string) old('sender_user_skill_id') === (string) $userSkill->id)>{{ $userSkill->skill->name }}</option>
                    @endforeach
                </select>
                @error('sender_user_skill_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="receiver_user_skill_id">ทักษะที่ต้องการแลกเปลี่ยนจากผู้รับ</label>
                <select class="form-select @error('receiver_user_skill_id') is-invalid @enderror" id="receiver_user_skill_id" name="receiver_user_skill_id" required>
                    <option value="">เลือกทักษะของผู้รับ</option>
                    @foreach ($receiver->userSkills as $userSkill)
                        <option value="{{ $userSkill->id }}" @selected((string) old('receiver_user_skill_id') === (string) $userSkill->id)>{{ $userSkill->skill->name }}</option>
                    @endforeach
                </select>
                @error('receiver_user_skill_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="learning_format">รูปแบบการเรียนรู้</label>
                <select class="form-select @error('learning_format') is-invalid @enderror" id="learning_format" name="learning_format" required>
                    <option value="">เลือกรูปแบบ</option>
                    <option value="online" @selected(old('learning_format') === 'online')>ออนไลน์</option>
                    <option value="onsite" @selected(old('learning_format') === 'onsite')>พบกันที่สถานที่จริง</option>
                    <option value="either" @selected(old('learning_format') === 'either')>ได้ทั้งสองรูปแบบ</option>
                </select>
                @error('learning_format') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label" for="preferred_schedule">ช่วงเวลาที่สะดวก</label>
                <input class="form-control @error('preferred_schedule') is-invalid @enderror" id="preferred_schedule" name="preferred_schedule" value="{{ old('preferred_schedule') }}" required>
                @error('preferred_schedule') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="message">ข้อความ</label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <button class="btn btn-primary mt-4" type="submit"><i class="bi bi-send me-1" aria-hidden="true"></i>ส่งคำขอ</button>
    </form>
@endsection
