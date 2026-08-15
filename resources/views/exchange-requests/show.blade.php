@extends('layouts.app')

@section('title', 'รายละเอียดคำขอแลกเปลี่ยน')
@section('page-title', 'รายละเอียดคำขอแลกเปลี่ยน')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">รายละเอียดคำขอแลกเปลี่ยน</h1>
            <p class="text-secondary mb-0">ข้อมูลคำขอของคุณ</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('exchange-requests.index') }}">กลับไปรายการคำขอ</a>
    </div>
    <section class="campus-card bg-white p-4">
        <dl class="row mb-0">
            <dt class="col-sm-4 mb-2">ผู้ส่ง</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->sender->name }}</dd>
            <dt class="col-sm-4 mb-2">ผู้รับ</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->receiver->name }}</dd>
            <dt class="col-sm-4 mb-2">ทักษะของผู้ส่ง</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->senderUserSkill->skill->name }}</dd>
            <dt class="col-sm-4 mb-2">ทักษะของผู้รับ</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->receiverUserSkill->skill->name }}</dd>
            <dt class="col-sm-4 mb-2">รูปแบบการเรียนรู้</dt>
            <dd class="col-sm-8 mb-3">{{ ['online' => 'ออนไลน์', 'onsite' => 'พบกันที่สถานที่จริง', 'either' => 'ได้ทั้งสองรูปแบบ'][$exchangeRequest->learning_format] }}</dd>
            <dt class="col-sm-4 mb-2">ช่วงเวลาที่สะดวก</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->preferred_schedule }}</dd>
            <dt class="col-sm-4 mb-2">ข้อความ</dt>
            <dd class="col-sm-8 mb-3">{{ $exchangeRequest->message }}</dd>
            <dt class="col-sm-4 mb-2">สถานะ</dt>
            <dd class="col-sm-8 mb-0">{{ $statusLabels[$exchangeRequest->status] }}</dd>
        </dl>
    </section>

    @canany(['accept', 'reject', 'cancel', 'complete'], $exchangeRequest)
        <section class="campus-card bg-white p-4 mt-4">
            <h2 class="h6 mb-3">การดำเนินการ</h2>
            <div class="d-flex flex-wrap gap-2">
                @can('accept', $exchangeRequest)
                    <form action="{{ route('exchange-requests.accept', $exchangeRequest) }}" method="POST" data-transition-form>
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-primary" type="submit">ตอบรับคำขอ</button>
                    </form>
                @endcan
                @can('reject', $exchangeRequest)
                    <form action="{{ route('exchange-requests.reject', $exchangeRequest) }}" method="POST" data-transition-form data-confirm="ยืนยันการปฏิเสธคำขอนี้หรือไม่">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-outline-danger" type="submit">ปฏิเสธคำขอ</button>
                    </form>
                @endcan
                @can('cancel', $exchangeRequest)
                    <form action="{{ route('exchange-requests.cancel', $exchangeRequest) }}" method="POST" data-transition-form data-confirm="ยืนยันการยกเลิกคำขอนี้หรือไม่">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-outline-danger" type="submit">ยกเลิกคำขอ</button>
                    </form>
                @endcan
                @can('complete', $exchangeRequest)
                    <form action="{{ route('exchange-requests.complete', $exchangeRequest) }}" method="POST" data-transition-form data-confirm="ยืนยันว่ากิจกรรมนี้เสร็จสิ้นแล้วหรือไม่">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-primary" type="submit">ทำเครื่องหมายว่าเสร็จสิ้น</button>
                    </form>
                @endcan
            </div>
        </section>
    @endcanany
@endsection
