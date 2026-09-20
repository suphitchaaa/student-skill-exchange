@extends('layouts.app')

@section('title', 'ภาพรวมผู้ดูแลระบบ')
@section('page-title', 'ภาพรวมผู้ดูแลระบบ')

@section('content')
    @php
        $requestStatusLabels = ['pending' => 'รอดำเนินการ', 'accepted' => 'ตอบรับแล้ว', 'rejected' => 'ปฏิเสธแล้ว', 'cancelled' => 'ยกเลิกแล้ว', 'completed' => 'เสร็จสิ้น'];
    @endphp
    <div class="admin-dashboard">
        <section class="dashboard-intro" aria-labelledby="admin-dashboard-title">
            <h1 id="admin-dashboard-title" class="mb-1">ภาพรวมระบบ</h1>
            <p class="text-secondary mb-0">สรุปข้อมูลจากรายการในระบบปัจจุบัน</p>
        </section>

        <section class="campus-card admin-metrics" aria-label="สรุปข้อมูลระบบ">
            @foreach ([
                ['icon' => 'bi-people', 'label' => 'นักศึกษาทั้งหมด', 'value' => $studentsCount],
                ['icon' => 'bi-person-check', 'label' => 'นักศึกษาที่ใช้งานอยู่', 'value' => $activeStudentsCount],
                ['icon' => 'bi-tags', 'label' => 'ทักษะที่ใช้งานอยู่', 'value' => $skillsCount],
                ['icon' => 'bi-person-x', 'label' => 'นักศึกษาที่ถูกระงับ', 'value' => $suspendedStudentsCount],
            ] as $metric)
                <article class="admin-metric">
                    <div class="admin-metric-heading">
                        <i class="bi {{ $metric['icon'] }}" aria-hidden="true"></i>
                        <p class="admin-metric-label">{{ $metric['label'] }}</p>
                    </div>
                    <p class="admin-metric-value">{{ $metric['value'] }}</p>
                </article>
            @endforeach
        </section>

        <div class="row g-3 admin-request-panels">
            <div class="col-12 col-lg-5">
                <section class="campus-card admin-panel">
                    <div class="admin-panel-header">
                        <h2 class="admin-panel-title">สรุปคำขอแลกเปลี่ยน</h2>
                    </div>
                    <div class="status-summary-list">
                        @foreach ($requestStatusLabels as $status => $label)
                            <div class="status-summary-row">
                                <span class="status-badge status-{{ $status }}">{{ $label }}</span>
                                <strong>{{ $requestStatusCounts[$status] ?? 0 }}</strong>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
            <div class="col-12 col-lg-7">
                <section class="campus-card admin-panel">
                    <div class="admin-panel-header">
                        <h2 class="admin-panel-title">คำขอล่าสุด</h2>
                        <span class="admin-panel-meta">ข้อมูลอ่านอย่างเดียว</span>
                    </div>
                    @forelse ($recentRequests as $request)
                        <div class="recent-request-row">
                            <div class="request-summary-main">
                                <span class="request-sender">{{ $request->sender->name }}</span>
                                <span class="request-recipient">ส่งถึง {{ $request->receiver->name }}</span>
                            </div>
                            <span class="status-badge status-{{ $request->status }}">{{ $requestStatusLabels[$request->status] ?? $request->status }}</span>
                        </div>
                    @empty
                        <p class="text-secondary mb-0">ยังไม่มีคำขอแลกเปลี่ยน</p>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
@endsection
