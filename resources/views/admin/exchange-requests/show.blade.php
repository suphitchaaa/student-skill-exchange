@extends('layouts.app')
@section('title', 'รายละเอียดคำขอแลกเปลี่ยน')
@section('page-title', 'รายละเอียดคำขอแลกเปลี่ยน')
@section('content')
    <div class="d-flex align-items-center gap-2 mb-4"><a href="{{ route('admin.exchange-requests.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left" aria-hidden="true"></i> กลับ</a><h1 class="h4 mb-0">รายละเอียดคำขอแลกเปลี่ยน</h1></div>
    <section class="campus-card bg-white p-4"><dl class="row mb-0">
        <dt class="col-sm-4 mb-2">ผู้ส่ง</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->sender->name }} ({{ $exchangeRequest->sender->email }})</dd>
        <dt class="col-sm-4 mb-2">ผู้รับ</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->receiver->name }} ({{ $exchangeRequest->receiver->email }})</dd>
        <dt class="col-sm-4 mb-2">ทักษะของผู้ส่ง</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->senderUserSkill->skill->name }}</dd>
        <dt class="col-sm-4 mb-2">ทักษะของผู้รับ</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->receiverUserSkill->skill->name }}</dd>
        <dt class="col-sm-4 mb-2">สถานะ</dt><dd class="col-sm-8 mb-3">{{ $statusLabels[$exchangeRequest->status] }}</dd>
        <dt class="col-sm-4 mb-2">รูปแบบการเรียนรู้</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->learning_format }}</dd>
        <dt class="col-sm-4 mb-2">ช่วงเวลาที่สะดวก</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->preferred_schedule }}</dd>
        <dt class="col-sm-4 mb-2">ข้อความ</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->message }}</dd>
        <dt class="col-sm-4 mb-2">วันที่ส่ง</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->created_at->format('d/m/Y H:i') }}</dd>
        <dt class="col-sm-4 mb-2">วันที่ตอบรับ</dt><dd class="col-sm-8 mb-3">{{ $exchangeRequest->responded_at?->format('d/m/Y H:i') ?? '-' }}</dd>
        <dt class="col-sm-4 mb-2">วันที่เสร็จสิ้น</dt><dd class="col-sm-8 mb-0">{{ $exchangeRequest->completed_at?->format('d/m/Y H:i') ?? '-' }}</dd>
    </dl></section>
@endsection
