<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserSkillRequest;
use App\Http\Requests\UpdateUserSkillRequest;
use App\Models\Skill;
use App\Models\UserSkill;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserSkillController extends Controller
{
    public function index(Request $request): View
    {
        $skillType = $request->query('type', 'offered');
        $skillType = in_array($skillType, ['offered', 'wanted'], true) ? $skillType : 'offered';

        return view('user-skills.index', [
            'activeSkillType' => $skillType,
            'availableSkills' => Skill::query()->where('is_active', true)->orderBy('name')->get(),
            'userSkills' => $request->user()->userSkills()->with('skill')->where('skill_type', $skillType)->latest()->get(),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function store(StoreUserSkillRequest $request): RedirectResponse
    {
        try {
            $request->user()->userSkills()->create($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->withErrors(['skill_id' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว']);
        }

        return to_route('user-skills.index', ['type' => $request->validated('skill_type')])
            ->with('success', 'เพิ่มทักษะเรียบร้อยแล้ว');
    }

    public function update(UpdateUserSkillRequest $request, UserSkill $userSkill): RedirectResponse
    {
        Gate::authorize('update', $userSkill);

        try {
            $userSkill->update($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()->withInput()->withErrors(['skill_id' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว']);
        }

        return to_route('user-skills.index', ['type' => $request->validated('skill_type')])
            ->with('success', 'แก้ไขทักษะเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, UserSkill $userSkill): RedirectResponse
    {
        Gate::authorize('delete', $userSkill);
        $skillType = $userSkill->skill_type;
        $userSkill->delete();

        return to_route('user-skills.index', ['type' => $skillType])->with('success', 'ลบทักษะเรียบร้อยแล้ว');
    }
}
