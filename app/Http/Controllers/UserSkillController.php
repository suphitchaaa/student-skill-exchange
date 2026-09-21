<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserSkillRequest;
use App\Http\Requests\UpdateUserSkillRequest;
use App\Models\Skill;
use App\Models\UserSkill;
use App\Services\UserSkillService;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserSkillController extends Controller
{
    private const DELETE_REFERENCED_MESSAGE = 'ไม่สามารถลบทักษะนี้ได้ เนื่องจากถูกใช้ในคำขอแลกเปลี่ยนหรือประวัติแล้ว';

    public function index(Request $request): View
    {
        $skillType = $request->query('type', 'offered');
        $skillType = in_array($skillType, ['offered', 'wanted'], true) ? $skillType : 'offered';

        return view('user-skills.index', [
            'activeSkillType' => $skillType,
            'availableSkills' => Skill::query()->where('is_active', true)->orderBy('name')->get(),
            'userSkills' => $request->user()->userSkills()
                ->with(['skill', 'historicalSkill'])
                ->withExists(['sentExchangeRequests', 'receivedExchangeRequests'])
                ->where('skill_type', $skillType)
                ->latest()
                ->get(),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function store(StoreUserSkillRequest $request, UserSkillService $service): RedirectResponse
    {
        $service->add($request->user(), $request->validated());

        return to_route('user-skills.index', ['type' => $request->validated('skill_type')])
            ->with('success', 'เพิ่มทักษะเรียบร้อยแล้ว');
    }

    public function update(UpdateUserSkillRequest $request, UserSkill $userSkill, UserSkillService $service): RedirectResponse
    {
        Gate::authorize('update', $userSkill);

        try {
            $service->update($userSkill, $request->validated());
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

        // เก็บทักษะที่ถูกอ้างอิงไว้เพื่อรักษาประวัติคำขอแลกเปลี่ยน
        if ($userSkill->sentExchangeRequests()->exists() || $userSkill->receivedExchangeRequests()->exists()) {
            return to_route('user-skills.index', ['type' => $skillType])
                ->with('error', self::DELETE_REFERENCED_MESSAGE);
        }

        try {
            $userSkill->delete();
        } catch (QueryException $exception) {
            // คำขอที่ถูกสร้างหลังตรวจอาจทำให้ FK ปฏิเสธการลบ
            if (($exception->errorInfo[0] ?? null) !== '23000' || (int) ($exception->errorInfo[1] ?? 0) !== 1451) {
                throw $exception;
            }

            return to_route('user-skills.index', ['type' => $skillType])
                ->with('error', self::DELETE_REFERENCED_MESSAGE);
        }

        return to_route('user-skills.index', ['type' => $skillType])->with('success', 'ลบทักษะเรียบร้อยแล้ว');
    }
}
