@extends('layouts.app')

@section('title', 'ภาพรวม')
@section('page-title', 'ภาพรวม')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">ยินดีต้อนรับ, {{ auth()->user()->name }}</h1>
        <p class="text-secondary mb-0">สรุปข้อมูลการแลกเปลี่ยนทักษะของคุณ</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-mortarboard" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">ทักษะที่สอนได้</p>
                <p class="h3 mb-0">{{ $offeredSkillsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-book" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">ทักษะที่ต้องการเรียน</p>
                <p class="h3 mb-0">{{ $wantedSkillsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-inbox" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">คำขอที่รอตอบกลับ</p>
                <p class="h3 mb-0">{{ $pendingRequestsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">กิจกรรมที่กำลังดำเนินการ</p>
                <p class="h3 mb-0">{{ $activeExchangesCount }}</p>
            </article>
        </div>
    </div>
@endsection
