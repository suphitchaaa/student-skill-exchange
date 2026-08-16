@extends('layouts.app')

@section('title', 'ภาพรวมผู้ดูแลระบบ')
@section('page-title', 'ภาพรวมผู้ดูแลระบบ')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">ภาพรวมระบบ</h1>
        <p class="text-secondary mb-0">สรุปข้อมูลจากรายการในระบบปัจจุบัน</p>
    </div>

    <div class="row g-3">
        @foreach ([
            ['icon' => 'bi-people', 'label' => 'นักศึกษาทั้งหมด', 'value' => $studentsCount],
            ['icon' => 'bi-person-check', 'label' => 'นักศึกษาที่ใช้งานอยู่', 'value' => $activeStudentsCount],
            ['icon' => 'bi-tags', 'label' => 'ทักษะที่ใช้งานอยู่', 'value' => $skillsCount],
            ['icon' => 'bi-person-x', 'label' => 'นักศึกษาที่ถูกระงับ', 'value' => $suspendedStudentsCount],
        ] as $metric)
            <div class="col-12 col-sm-6 col-xl-3">
                <article class="campus-card metric-card bg-white p-3">
                    <div class="metric-icon mb-3"><i class="bi {{ $metric['icon'] }}" aria-hidden="true"></i></div>
                    <p class="text-secondary small mb-1">{{ $metric['label'] }}</p>
                    <p class="h3 mb-0">{{ $metric['value'] }}</p>
                </article>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-5">
            <section class="campus-card bg-white p-3 h-100">
                <h2 class="h6 mb-3">สรุปคำขอแลกเปลี่ยน</h2>
                <div class="row g-2">
                    @foreach (['pending' => 'รอดำเนินการ', 'accepted' => 'ตอบรับแล้ว', 'rejected' => 'ปฏิเสธแล้ว', 'cancelled' => 'ยกเลิกแล้ว', 'completed' => 'เสร็จสิ้น'] as $status => $label)
                        <div class="col-6">
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span class="text-secondary">{{ $label }}</span>
                                <strong>{{ $requestStatusCounts[$status] ?? 0 }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
        <div class="col-12 col-lg-7">
            <section class="campus-card bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0">คำขอล่าสุด</h2>
                    <span class="text-secondary small">ข้อมูลอ่านอย่างเดียว</span>
                </div>
                @forelse ($recentRequests as $request)
                    <div class="d-flex justify-content-between gap-3 border-bottom py-2">
                        <div class="text-truncate">
                            <span class="fw-semibold">{{ $request->sender->name }}</span>
                            <span class="text-secondary">ส่งถึง {{ $request->receiver->name }}</span>
                        </div>
                        <span class="badge text-bg-light text-nowrap">{{ $request->status }}</span>
                    </div>
                @empty
                    <p class="text-secondary mb-0">ยังไม่มีคำขอแลกเปลี่ยน</p>
                @endforelse
            </section>
        </div>
    </div>
@endsection
