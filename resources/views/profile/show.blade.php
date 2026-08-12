@extends('layouts.app')

@section('title', 'โปรไฟล์ของฉัน')
@section('page-title', 'โปรไฟล์ของฉัน')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">ข้อมูลนักศึกษา</h1>
            <p class="text-secondary mb-0">ข้อมูลนี้ช่วยให้เพื่อนนักศึกษารู้จักคุณมากขึ้น</p>
        </div>
        <a class="btn btn-primary" href="{{ route('profile.edit') }}">
            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>แก้ไขโปรไฟล์
        </a>
    </div>

    <section class="campus-card bg-white p-4">
        <div class="row g-4">
            <div class="col-12 col-md-3 text-center">
                @if ($profileImageUrl)
                    <img class="profile-image" src="{{ $profileImageUrl }}" alt="รูปโปรไฟล์ของ {{ auth()->user()->name }}">
                @else
                    <div class="profile-image-placeholder mx-auto" aria-hidden="true"><i class="bi bi-person"></i></div>
                @endif
            </div>
            <div class="col-12 col-md-9">
                <h2 class="h5 mb-3">{{ auth()->user()->name }}</h2>
                <dl class="row mb-0 profile-details">
                    <dt class="col-sm-4">คณะ</dt><dd class="col-sm-8">{{ $profile->faculty ?? '-' }}</dd>
                    <dt class="col-sm-4">สาขาวิชา</dt><dd class="col-sm-8">{{ $profile->major ?? '-' }}</dd>
                    <dt class="col-sm-4">ชั้นปี</dt><dd class="col-sm-8">{{ $profile->year_level ? 'ชั้นปี '.$profile->year_level : '-' }}</dd>
                    <dt class="col-sm-4">เบอร์โทรศัพท์</dt><dd class="col-sm-8">{{ $profile->phone ?? '-' }}</dd>
                    <dt class="col-sm-4">ช่องทางติดต่อ</dt><dd class="col-sm-8">{{ $profile->contact_channel ?? '-' }}</dd>
                    <dt class="col-sm-4">แนะนำตัว</dt><dd class="col-sm-8 text-break">{{ $profile->bio ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </section>
@endsection
