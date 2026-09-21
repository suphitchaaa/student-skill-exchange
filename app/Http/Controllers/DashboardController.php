<?php

namespace App\Http\Controllers;

use App\Queries\StudentMatchQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, StudentMatchQuery $studentMatchQuery): View
    {
        $user = $request->user();

        return view('dashboard.student', [
            'offeredSkillsCount' => $user->userSkills()->where('skill_type', 'offered')->count(),
            'wantedSkillsCount' => $user->userSkills()->where('skill_type', 'wanted')->count(),
            'pendingRequestsCount' => $user->receivedExchangeRequests()->where('status', 'pending')->count(),
            'activeExchangesCount' => $user->sentExchangeRequests()->where('status', 'accepted')->count()
                + $user->receivedExchangeRequests()->where('status', 'accepted')->count(),
            'recommendedStudents' => $studentMatchQuery->dashboardRecommendationsFor($user, 3),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }
}
