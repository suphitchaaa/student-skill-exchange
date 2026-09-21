@extends('layouts.app')

@section('title', 'แก้ไขโปรไฟล์')
@section('page-title', 'แก้ไขโปรไฟล์')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">แก้ไขข้อมูลนักศึกษา</h1>
            <p class="text-secondary mb-0">กรอกข้อมูลที่ต้องการแสดงในโปรไฟล์ของคุณ</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('profile.show') }}">กลับไปดูโปรไฟล์</a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <form class="campus-card bg-white p-4" action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="faculty">คณะ</label>
                        <input class="form-control @error('faculty') is-invalid @enderror" id="faculty" name="faculty" maxlength="255" value="{{ old('faculty', $profile->faculty) }}">
                        @error('faculty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="major">สาขาวิชา</label>
                        <input class="form-control @error('major') is-invalid @enderror" id="major" name="major" maxlength="255" value="{{ old('major', $profile->major) }}">
                        @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="year_level">ชั้นปี</label>
                        <input class="form-control @error('year_level') is-invalid @enderror" id="year_level" name="year_level" type="number" min="1" max="8" value="{{ old('year_level', $profile->year_level) }}">
                        @error('year_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="phone">เบอร์โทรศัพท์</label>
                        <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" maxlength="255" value="{{ old('phone', $profile->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="contact_channel">ช่องทางติดต่อ</label>
                        <input class="form-control @error('contact_channel') is-invalid @enderror" id="contact_channel" name="contact_channel" maxlength="255" value="{{ old('contact_channel', $profile->contact_channel) }}">
                        @error('contact_channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="bio">แนะนำตัว</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $profile->bio) }}</textarea>
                        @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mt-4"><button class="btn btn-primary" type="submit">บันทึกข้อมูล</button></div>
            </form>
        </div>
        <div class="col-12 col-xl-4">
            <section class="campus-card bg-white p-4">
                <h2 class="h5">รูปโปรไฟล์</h2>
                <p class="text-secondary small">รองรับ JPG, JPEG, PNG และ WEBP ขนาดไม่เกิน 2 MB</p>
                @if ($profileImageUrl)
                    <img class="profile-image mb-3" src="{{ $profileImageUrl }}" alt="รูปโปรไฟล์ของ {{ auth()->user()->name }}">
                @else
                    <div class="profile-image-placeholder mb-3" aria-hidden="true"><i class="bi bi-person"></i></div>
                @endif
                <form action="{{ route('profile.image.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="form-label" for="profile_image">เลือกรูปโปรไฟล์</label>
                    <input class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                    @error('profile_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <button class="btn btn-primary mt-3" type="submit">อัปโหลดรูป</button>
                </form>
                @if ($profile->profile_image)
                    <form action="{{ route('profile.image.destroy') }}" method="POST" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit">ลบรูปโปรไฟล์</button>
                    </form>
                @endif
            </section>
        </div>
    </div>
@endsection
