<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateExchangeRequestException;
use App\Http\Requests\StoreExchangeRequest;
use App\Models\ExchangeRequest;
use App\Models\User;
use App\Services\ExchangeRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ExchangeRequestController extends Controller
{
    public function create(Request $request, User $user): View
    {
        abort_unless($user->role === 'student' && $user->status === 'active' && $user->id !== $request->user()->id, 404);

        return view('exchange-requests.create', [
            'receiver' => $user->load(['studentProfile', 'userSkills' => fn ($query) => $query
                ->where('skill_type', 'offered')
                ->whereHas('skill', fn ($skillQuery) => $skillQuery->where('is_active', true))
                ->with('skill')]),
            'senderSkills' => $request->user()->userSkills()
                ->where('skill_type', 'offered')
                ->whereHas('skill', fn ($skillQuery) => $skillQuery->where('is_active', true))
                ->with('skill')
                ->get(),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function store(StoreExchangeRequest $request, User $user, ExchangeRequestService $service): RedirectResponse
    {
        abort_unless($user->role === 'student' && $user->status === 'active' && $user->id !== $request->user()->id, 404);

        try {
            $service->create($request->user(), $user, $request->validated());
        } catch (DuplicateExchangeRequestException) {
            return back()->withInput()->withErrors(['receiver_user_skill_id' => 'คุณมีคำขอที่รอดำเนินการสำหรับทักษะคู่นี้แล้ว']);
        }

        return to_route('exchange-requests.index', ['tab' => 'sent'])->with('success', 'ส่งคำขอแลกเปลี่ยนเรียบร้อยแล้ว');
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'sent');
        $tab = in_array($tab, ['sent', 'received', 'history'], true) ? $tab : 'sent';
        $userId = $request->user()->id;

        $query = ExchangeRequest::query()->with([
            'sender',
            'receiver',
            'senderUserSkill.skill',
            'receiverUserSkill.skill',
        ]);

        if ($tab === 'sent') {
            $query->where('sender_id', $userId)->where('status', 'pending');
        } elseif ($tab === 'received') {
            $query->where('receiver_id', $userId)->where('status', 'pending');
        } else {
            $query->where(fn ($scope) => $scope
                ->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId))
                ->where('status', '!=', 'pending');
        }

        return view('exchange-requests.index', [
            'requests' => $query->latest()->get(),
            'activeTab' => $tab,
            'statusLabels' => $this->statusLabels(),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function show(Request $request, ExchangeRequest $exchangeRequest): View
    {
        Gate::authorize('view', $exchangeRequest);
        $exchangeRequest->load(['sender.studentProfile', 'receiver.studentProfile', 'senderUserSkill.skill', 'receiverUserSkill.skill']);

        return view('exchange-requests.show', [
            'exchangeRequest' => $exchangeRequest,
            'statusLabels' => $this->statusLabels(),
            'dashboardRoute' => route('student.dashboard'),
            'dashboardRouteName' => 'student.dashboard',
            'roleLabel' => 'พื้นที่นักศึกษา',
        ]);
    }

    public function accept(Request $request, ExchangeRequest $exchangeRequest, ExchangeRequestService $service): RedirectResponse
    {
        Gate::authorize('accept', $exchangeRequest);
        $service->accept($request->user(), $exchangeRequest);

        return to_route('exchange-requests.show', $exchangeRequest)->with('success', 'ตอบรับคำขอเรียบร้อยแล้ว');
    }

    public function reject(Request $request, ExchangeRequest $exchangeRequest, ExchangeRequestService $service): RedirectResponse
    {
        Gate::authorize('reject', $exchangeRequest);
        $service->reject($request->user(), $exchangeRequest);

        return to_route('exchange-requests.show', $exchangeRequest)->with('success', 'ปฏิเสธคำขอเรียบร้อยแล้ว');
    }

    public function cancel(Request $request, ExchangeRequest $exchangeRequest, ExchangeRequestService $service): RedirectResponse
    {
        Gate::authorize('cancel', $exchangeRequest);
        $service->cancel($request->user(), $exchangeRequest);

        return to_route('exchange-requests.show', $exchangeRequest)->with('success', 'ยกเลิกคำขอเรียบร้อยแล้ว');
    }

    public function complete(Request $request, ExchangeRequest $exchangeRequest, ExchangeRequestService $service): RedirectResponse
    {
        Gate::authorize('complete', $exchangeRequest);
        $service->complete($request->user(), $exchangeRequest);

        return to_route('exchange-requests.show', $exchangeRequest)->with('success', 'บันทึกกิจกรรมเสร็จสิ้นแล้ว');
    }

    private function statusLabels(): array
    {
        return [
            'pending' => 'รอดำเนินการ',
            'accepted' => 'ตอบรับแล้ว',
            'rejected' => 'ปฏิเสธแล้ว',
            'cancelled' => 'ยกเลิกแล้ว',
            'completed' => 'เสร็จสิ้นแล้ว',
        ];
    }
}
