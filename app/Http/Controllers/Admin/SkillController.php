<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    public function index(Request $request): View
    {
        $skills = Skill::query()
            ->withTrashed()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->toString().'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')->toString()))
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->string('status')->toString() === 'deleted') {
                    $query->whereNotNull('deleted_at');
                } elseif ($request->string('status')->toString() === 'inactive') {
                    $query->where('is_active', false)->whereNull('deleted_at');
                } elseif ($request->string('status')->toString() === 'active') {
                    $query->where('is_active', true)->whereNull('deleted_at');
                }
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.skills.index', [
            'skills' => $skills,
            'categories' => Skill::query()->withTrashed()->distinct()->orderBy('category')->pluck('category'),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function create(): View
    {
        return view('admin.skills.create', [
            'skill' => new Skill(['is_active' => true]),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function store(StoreSkillRequest $request): RedirectResponse
    {
        try {
            Skill::query()->create($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->handleDuplicateName($exception);
        }

        return to_route('admin.skills.index')->with('status', 'เพิ่มทักษะเรียบร้อยแล้ว');
    }

    public function edit(Skill $skill): View
    {
        return view('admin.skills.edit', [
            'skill' => $skill,
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function update(UpdateSkillRequest $request, Skill $skill): RedirectResponse
    {
        try {
            $skill->update($request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            return $this->handleDuplicateName($exception);
        }

        return to_route('admin.skills.index')->with('status', 'แก้ไขทักษะเรียบร้อยแล้ว');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        // Soft delete รักษา user_skills และประวัติคำขอที่อ้างอิงทักษะเดิมไว้
        $skill->delete();

        return to_route('admin.skills.index')->with('status', 'ปิดการใช้งานทักษะเรียบร้อยแล้ว');
    }

    private function handleDuplicateName(UniqueConstraintViolationException $exception): RedirectResponse
    {
        // ระหว่าง validation กับบันทึก อาจมีชื่อเดียวกันถูกเพิ่มจากอีกคำขอ
        if (! str_contains($exception->getMessage(), 'skills_normalized_name_unique')
            && ! str_contains($exception->getMessage(), 'skills.normalized_name')) {
            throw $exception;
        }

        return back()->withInput()->withErrors(['name' => 'ชื่อทักษะนี้มีอยู่แล้ว']);
    }
}
