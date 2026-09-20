@extends('layouts.app')

@section('title', 'คำขอแลกเปลี่ยน')
@section('page-title', 'คำขอแลกเปลี่ยน')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    <div class="content-heading">
        <h1 class="mb-1">คำขอแลกเปลี่ยน</h1>
        <p class="text-secondary mb-0">ติดตามคำขอที่ส่ง ได้รับ และประวัติของคุณ</p>
    </div>
    <ul class="nav nav-tabs workflow-tabs mb-4">
        @foreach (['sent' => 'ส่งแล้ว', 'received' => 'ได้รับ', 'history' => 'ประวัติ'] as $tab => $label)
            <li class="nav-item"><a class="nav-link {{ $activeTab === $tab ? 'active' : '' }}" href="{{ route('exchange-requests.index', ['tab' => $tab]) }}">{{ $label }}</a></li>
        @endforeach
    </ul>

    @forelse ($requests as $exchangeRequest)
        @php($isSender = $exchangeRequest->sender_id === auth()->id())
        <article class="campus-card bg-white request-list-card">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <p class="small text-secondary mb-1">{{ $isSender ? 'ส่งถึง' : 'ได้รับจาก' }} {{ $isSender ? $exchangeRequest->receiver->name : $exchangeRequest->sender->name }}</p>
                    <h2 class="h6 mb-2">{{ $exchangeRequest->senderUserSkill->historicalSkill->name }} แลกกับ {{ $exchangeRequest->receiverUserSkill->historicalSkill->name }}</h2>
                    <span class="status-badge status-{{ $exchangeRequest->status }}">{{ $statusLabels[$exchangeRequest->status] }}</span>
                </div>
                <a class="btn btn-outline-primary align-self-start" href="{{ route('exchange-requests.show', $exchangeRequest) }}">ดูรายละเอียด</a>
            </div>
        </article>
    @empty
        <section class="campus-card bg-white content-empty-state text-center">
            <i class="bi bi-inbox fs-2 text-secondary" aria-hidden="true"></i>
            <h2 class="h5 mt-3">ยังไม่มีคำขอในหมวดนี้</h2>
            <p class="text-secondary mb-0">รายการคำขอของคุณจะแสดงที่นี่</p>
        </section>
    @endforelse
@endsection
