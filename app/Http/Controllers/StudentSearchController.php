<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentSearchController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $skillId = $request->integer('skill_id') ?: null;
        $faculty = trim((string) $request->query('faculty', ''));
        $yearLevel = $request->integer('year_level') ?: null;

        $students = User::query()
            ->with([
                'studentProfile',
                'userSkills' => fn ($query) => $query->whereHas('skill', fn ($skillQuery) => $skillQuery->where('is_active', true))->with('skill'),
            ])
            ->where('role', 'student')
            ->where('status', 'active')
            ->whereKeyNot($request->user()->id)
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($faculty !== '', fn ($query) => $query->whereHas('studentProfile', fn ($profileQuery) => $profileQuery->where('faculty', $faculty)))
            ->when($yearLevel !== null, fn ($query) => $query->whereHas('studentProfile', fn ($profileQuery) => $profileQuery->where('year_level', $yearLevel)))
            ->when($skillId !== null, fn ($query) => $query->whereHas('userSkills', fn ($userSkillQuery) => $userSkillQuery
                ->where('skill_id', $skillId)
                ->whereHas('skill', fn ($skillQuery) => $skillQuery->where('is_active', true))))
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $availableSkills = Skill::query()->where('is_active', true)->orderBy('name')->get();
        $faculties = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->whereHas('studentProfile', fn ($query) => $query->whereNotNull('faculty')->where('faculty', '<>', ''))
            ->with('studentProfile:id,user_id,faculty')
            ->get()
            ->pluck('studentProfile.faculty')
            ->unique()
            ->sort()
            ->values();

        return view('students.index', [
            'students' => $students,
            'availableSkills' => $availableSkills,
            'faculties' => $faculties,
            'search' => $search,
            'skillId' => $skillId,
            'faculty' => $faculty,
            'yearLevel' => $yearLevel,
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function show(Request $request, User $user): View
    {
        abort_unless($user->role === 'student' && $user->status === 'active', 404);

        $user->load([
            'studentProfile',
            'userSkills' => fn ($query) => $query
                ->whereHas('skill', fn ($skillQuery) => $skillQuery->where('is_active', true))
                ->with('skill')
                ->orderBy('skill_type')
                ->orderBy('id'),
        ]);

        return view('students.show', [
            'student' => $user,
            'profileImageUrl' => $user->studentProfile?->profile_image === null
                ? null
                : asset('storage/'.ltrim($user->studentProfile->profile_image, '/')),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }
}
