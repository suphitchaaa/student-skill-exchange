@extends('layouts.app')
@section('title', 'แก้ไขทักษะ')
@section('page-title', 'แก้ไขทักษะ')
@section('content')
    <div class="d-flex align-items-center gap-2 mb-4"><a href="{{ route('admin.skills.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left" aria-hidden="true"></i> กลับ</a><h1 class="h4 mb-0">แก้ไขทักษะ</h1></div>
    @include('admin.skills.form', ['action' => route('admin.skills.update', $skill), 'method' => 'PUT', 'submitLabel' => 'บันทึกการแก้ไข'])
@endsection
