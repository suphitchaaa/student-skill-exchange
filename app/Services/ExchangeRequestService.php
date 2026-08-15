<?php

namespace App\Services;

use App\Exceptions\DuplicateExchangeRequestException;
use App\Models\ExchangeRequest;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExchangeRequestService
{
    public function create(User $sender, User $receiver, array $data): ExchangeRequest
    {
        return DB::transaction(function () use ($sender, $receiver, $data): ExchangeRequest {
            if ($sender->id === $receiver->id) {
                throw ValidationException::withMessages(['receiver_id' => 'ไม่สามารถส่งคำขอให้ตนเองได้']);
            }

            if ($sender->role !== 'student' || $sender->status !== 'active') {
                throw ValidationException::withMessages(['sender_user_skill_id' => 'บัญชีผู้ส่งไม่สามารถส่งคำขอได้']);
            }

            if ($receiver->role !== 'student' || $receiver->status !== 'active') {
                throw ValidationException::withMessages(['receiver_user_skill_id' => 'ไม่สามารถส่งคำขอให้บัญชีนี้ได้']);
            }

            $senderSkill = $this->offeredSkill($data['sender_user_skill_id'], $sender->id);
            $receiverSkill = $this->offeredSkill($data['receiver_user_skill_id'], $receiver->id);

            if ($senderSkill === null) {
                throw ValidationException::withMessages(['sender_user_skill_id' => 'กรุณาเลือกทักษะที่คุณสอนได้']);
            }

            if ($receiverSkill === null) {
                throw ValidationException::withMessages(['receiver_user_skill_id' => 'กรุณาเลือกทักษะที่นักศึกษาปลายทางสอนได้']);
            }

            // ล็อกชุดคำขอก่อนตรวจซ้ำ เพื่อให้กติกา Pending ใช้เงื่อนไข 4 ฟิลด์ตรงกันครบถ้วน
            $duplicate = ExchangeRequest::query()
                ->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id)
                ->where('sender_user_skill_id', $senderSkill->id)
                ->where('receiver_user_skill_id', $receiverSkill->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();

            if ($duplicate) {
                throw new DuplicateExchangeRequestException;
            }

            return ExchangeRequest::query()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'sender_user_skill_id' => $senderSkill->id,
                'receiver_user_skill_id' => $receiverSkill->id,
                'learning_format' => $data['learning_format'],
                'preferred_schedule' => $data['preferred_schedule'],
                'message' => $data['message'],
                'status' => 'pending',
            ]);
        });
    }

    private function offeredSkill(int $userSkillId, int $userId): ?UserSkill
    {
        return UserSkill::query()
            ->whereKey($userSkillId)
            ->where('user_id', $userId)
            ->where('skill_type', 'offered')
            ->whereHas('skill', fn ($query) => $query->where('is_active', true))
            ->with('skill')
            ->first();
    }
}
