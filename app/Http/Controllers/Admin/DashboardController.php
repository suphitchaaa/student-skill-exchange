<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.admin', [
            'activeStudentsCount' => User::query()->where('role', 'student')->where('status', 'active')->count(),
            'suspendedStudentsCount' => User::query()->where('role', 'student')->where('status', 'suspended')->count(),
            'activeSkillsCount' => Skill::query()->where('is_active', true)->count(),
            'pendingRequestsCount' => ExchangeRequest::query()->where('status', 'pending')->count(),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'พื้นที่ผู้ดูแลระบบ',
        ]);
    }
}
