<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExchangeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = ExchangeRequest::query()->with([
            'sender', 'receiver', 'senderUserSkill.skill', 'receiverUserSkill.skill',
        ]);

        $query->when($request->filled('q'), function ($query) use ($request) {
            $search = $request->string('q')->toString();
            $query->where(function ($scope) use ($search) {
                $scope->whereHas('sender', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('receiver', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('senderUserSkill.skill', fn ($skill) => $skill->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('receiverUserSkill.skill', fn ($skill) => $skill->where('name', 'like', "%{$search}%"));
            });
        });
        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')));
        $query->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')));

        return view('admin.exchange-requests.index', [
            'requests' => $query->latest()->paginate(15)->withQueryString(),
            'statusLabels' => $this->statusLabels(),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    public function show(ExchangeRequest $exchangeRequest): View
    {
        $exchangeRequest->load(['sender', 'receiver', 'senderUserSkill.skill', 'receiverUserSkill.skill']);

        return view('admin.exchange-requests.show', [
            'exchangeRequest' => $exchangeRequest,
            'statusLabels' => $this->statusLabels(),
            'dashboardRoute' => route('admin.dashboard'),
            'dashboardRouteName' => 'admin.dashboard',
            'roleLabel' => 'ผู้ดูแลระบบ',
        ]);
    }

    private function statusLabels(): array
    {
        return [
            'pending' => 'รอดำเนินการ', 'accepted' => 'ตอบรับแล้ว', 'rejected' => 'ปฏิเสธแล้ว',
            'cancelled' => 'ยกเลิกแล้ว', 'completed' => 'เสร็จสิ้นแล้ว',
        ];
    }
}
