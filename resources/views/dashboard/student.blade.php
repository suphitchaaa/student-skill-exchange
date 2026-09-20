@extends('layouts.app')

@section('title', 'ภาพรวม')
@section('page-title', 'ภาพรวม')

@section('content')
    <div class="student-dashboard">
        <section class="dashboard-intro dashboard-intro--student" aria-labelledby="student-dashboard-title">
            <div>
                <h1 id="student-dashboard-title" class="mb-1">ยินดีต้อนรับ, <span class="welcome-name">{{ auth()->user()->name }}</span></h1>
                <p class="mb-0">สรุปข้อมูลการแลกเปลี่ยนทักษะของคุณ</p>
            </div>
        </section>

        <section class="row g-3 student-metrics" aria-label="สรุปข้อมูลของคุณ">
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="campus-card student-metric student-metric--feature">
                    <div class="student-metric-heading">
                        <i class="bi bi-mortarboard" aria-hidden="true"></i>
                        <p class="student-metric-label">ทักษะที่สอนได้</p>
                    </div>
                    <p class="student-metric-value">{{ $offeredSkillsCount }}</p>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="campus-card student-metric student-metric--learning">
                    <div class="student-metric-heading">
                        <i class="bi bi-book" aria-hidden="true"></i>
                        <p class="student-metric-label">ทักษะที่ต้องการเรียน</p>
                    </div>
                    <p class="student-metric-value">{{ $wantedSkillsCount }}</p>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="campus-card student-metric student-metric--pending">
                    <div class="student-metric-heading">
                        <i class="bi bi-inbox" aria-hidden="true"></i>
                        <p class="student-metric-label">คำขอที่รอตอบกลับ</p>
                    </div>
                    <p class="student-metric-value">{{ $pendingRequestsCount }}</p>
                </article>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="campus-card student-metric student-metric--active">
                    <div class="student-metric-heading">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                        <p class="student-metric-label">กิจกรรมที่กำลังดำเนินการ</p>
                    </div>
                    <p class="student-metric-value">{{ $activeExchangesCount }}</p>
                </article>
            </div>
        </section>
    </div>
@endsection
