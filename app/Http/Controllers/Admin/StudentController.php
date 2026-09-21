<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::query()
            ->where('role', 'student')
            ->with('studentProfile')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q')->toString();
                $query->where(function ($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('faculty'), fn ($query) => $query->whereHas('studentProfile', fn ($profile) => $profile->where('faculty', $request->string('faculty'))))
            ->when($request->filled('year_level'), fn ($query) => $query->whereHas('studentProfile', fn ($profile) => $profile->where('year_level', $request->integer('year_level'))))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'faculties' => User::query()->where('role', 'student')->whereHas('studentProfile')->with('studentProfile')->get()->pluck('studentProfile.faculty')->filter()->unique()->sort()->values(),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function show(User $user): View
    {
        $this->ensureStudent($user);
        $user->load(['studentProfile', 'userSkills.historicalSkill']);

        return view('admin.students.show', [
            'student' => $user,
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function suspend(User $user): RedirectResponse
    {
        $this->ensureStudent($user);
        // บันทึกสถานะลง users เพื่อให้ account.active ตรวจพบในการร้องขอถัดไปทันที
        $user->update(['status' => 'suspended']);

        return back()->with('status', 'ระงับบัญชีนักศึกษาเรียบร้อยแล้ว');
    }

    public function activate(User $user): RedirectResponse
    {
        $this->ensureStudent($user);
        $user->update(['status' => 'active']);

        return back()->with('status', 'เปิดใช้งานบัญชีนักศึกษาเรียบร้อยแล้ว');
    }

    private function ensureStudent(User $user): void
    {
        abort_unless($user->role === 'student', 404);
    }
}
