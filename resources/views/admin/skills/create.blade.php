@extends('layouts.app')
@section('title', 'เพิ่มทักษะ')
@section('page-title', 'เพิ่มทักษะ')
@section('content')
    <div class="d-flex align-items-center gap-2 mb-4"><a href="{{ route('admin.skills.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left" aria-hidden="true"></i> กลับ</a><h1 class="h4 mb-0">เพิ่มทักษะส่วนกลาง</h1></div>
    @include('admin.skills.form', ['action' => route('admin.skills.store'), 'method' => 'POST', 'submitLabel' => 'เพิ่มทักษะ'])
@endsection
