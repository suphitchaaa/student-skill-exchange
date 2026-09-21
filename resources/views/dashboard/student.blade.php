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

        <section class="mt-4" aria-labelledby="recommended-students-title">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <h2 id="recommended-students-title" class="h4 mb-1">คนที่อาจเหมาะกับคุณ</h2>
                    <p class="text-secondary mb-0">นักศึกษาที่มีทักษะตรงกับสิ่งที่คุณกำลังมองหา</p>
                </div>
            </div>

            @if ($recommendedStudents->isEmpty())
                <div class="campus-card p-4">
                    <p class="fw-semibold mb-1">ยังไม่มีคนที่ตรงกับทักษะที่คุณกำลังมองหา</p>
                    <p class="text-secondary mb-3">ลองเพิ่มหรือปรับทักษะที่ต้องการเรียนเพื่อให้ระบบแนะนำได้ตรงขึ้น</p>
                    <a class="btn btn-outline-secondary" href="{{ route('user-skills.index', ['type' => 'wanted']) }}">
                        จัดการทักษะที่ต้องการเรียน
                    </a>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($recommendedStudents as $recommendedStudent)
                        @php
                            $matchingSkills = $recommendedStudent->userSkills;
                            $visibleSkills = $matchingSkills->take(3);
                            $remainingSkillsCount = max(0, $matchingSkills->count() - 3);
                        @endphp

                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="campus-card student-result-card h-100 p-3">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                    <div>
                                        <h3 class="h5 mb-1">{{ $recommendedStudent->name }}</h3>
                                        @if ($recommendedStudent->studentProfile)
                                            <p class="text-secondary mb-0">
                                                {{ $recommendedStudent->studentProfile->faculty }}
                                                @if ($recommendedStudent->studentProfile->year_level)
                                                    · ชั้นปี {{ $recommendedStudent->studentProfile->year_level }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>

                                    @if ($recommendedStudent->is_mutual_match)
                                        <span class="badge bg-success-subtle text-success-emphasis border">ตรงกันทั้งสองทาง</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis border">มีทักษะที่คุณกำลังมองหา</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <p class="small fw-semibold mb-2">ทักษะที่ตรงกับสิ่งที่คุณต้องการเรียน</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($visibleSkills as $userSkill)
                                            <span class="badge bg-light text-dark border">{{ $userSkill->skill->name }}</span>
                                        @endforeach
                                        @if ($remainingSkillsCount > 0)
                                            <span class="small text-secondary align-self-center">และอีก {{ $remainingSkillsCount }} ทักษะ</span>
                                        @endif
                                    </div>
                                </div>

                                <a class="btn btn-outline-secondary" href="{{ route('students.show', $recommendedStudent) }}">
                                    ดูโปรไฟล์
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
