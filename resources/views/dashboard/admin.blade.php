@extends('layouts.app')

@section('title', 'ภาพรวมผู้ดูแลระบบ')
@section('page-title', 'ภาพรวมผู้ดูแลระบบ')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">ภาพรวมระบบ</h1>
        <p class="text-secondary mb-0">สถิติปัจจุบันจากข้อมูลในระบบ</p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-people" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">นักศึกษาที่ใช้งานอยู่</p>
                <p class="h3 mb-0">{{ $activeStudentsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-person-x" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">บัญชีที่ถูกระงับ</p>
                <p class="h3 mb-0">{{ $suspendedStudentsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-tags" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">ทักษะที่เปิดใช้งาน</p>
                <p class="h3 mb-0">{{ $activeSkillsCount }}</p>
            </article>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="campus-card metric-card bg-white p-3">
                <div class="metric-icon mb-3"><i class="bi bi-hourglass-split" aria-hidden="true"></i></div>
                <p class="text-secondary small mb-1">คำขอที่รอดำเนินการ</p>
                <p class="h3 mb-0">{{ $pendingRequestsCount }}</p>
            </article>
        </div>
    </div>
@endsection
