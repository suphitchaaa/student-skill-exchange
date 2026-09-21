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
        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ], [
            'date_from.date_format' => 'วันที่เริ่มต้นต้องเป็นวันที่ที่ถูกต้อง',
            'date_to.date_format' => 'วันที่สิ้นสุดต้องเป็นวันที่ที่ถูกต้อง',
            'date_to.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
        ]);

        $query = ExchangeRequest::query()->with([
            'sender', 'receiver', 'senderUserSkill.skill', 'receiverUserSkill.skill',
        ]);

        $query->when($request->filled('q'), function ($query) use ($request) {
            $search = $request->string('q')->toString();
            $query->where(function ($scope) use ($search) {
                $scope->whereHas('sender', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('receiver', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('senderUserSkill.historicalSkill', fn ($skill) => $skill->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('receiverUserSkill.historicalSkill', fn ($skill) => $skill->where('name', 'like', "%{$search}%"));
            });
        });
        $query->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()));
        $query->when($validated['date_from'] ?? null, fn ($query, $dateFrom) => $query->whereDate('created_at', '>=', $dateFrom));
        $query->when($validated['date_to'] ?? null, fn ($query, $dateTo) => $query->whereDate('created_at', '<=', $dateTo));

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
