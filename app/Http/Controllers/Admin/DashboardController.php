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
        $requestStatusCounts = ExchangeRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard.admin', [
            'studentsCount' => User::query()->where('role', 'student')->count(),
            'activeStudentsCount' => User::query()->where('role', 'student')->where('status', 'active')->count(),
            'suspendedStudentsCount' => User::query()->where('role', 'student')->where('status', 'suspended')->count(),
            'skillsCount' => Skill::query()->where('is_active', true)->whereNull('deleted_at')->count(),
            'requestStatusCounts' => $requestStatusCounts,
            'recentRequests' => ExchangeRequest::query()
                ->with(['sender', 'receiver'])
                ->latest()
                ->limit(8)
                ->get(),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'พื้นที่ผู้ดูแลระบบ',
        ]);
    }
}
