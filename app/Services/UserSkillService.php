<?php

namespace App\Services;

use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserSkillService
{
    public function add(User $user, array $data): UserSkill
    {
        return DB::transaction(function () use ($user, $data): UserSkill {
            $skill = isset($data['skill_id'])
                ? $this->existingSkill($data['skill_id'])
                : $this->findOrCreateSkill($data['skill_name']);

            try {
                return $user->userSkills()->create([
                    'skill_id' => $skill->id,
                    'skill_type' => $data['skill_type'],
                    'description' => $data['description'] ?? null,
                ]);
            } catch (UniqueConstraintViolationException) {
                $field = isset($data['skill_id']) ? 'skill_id' : 'skill_name';

                throw ValidationException::withMessages([$field => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว']);
            }
        }, 3);
    }

    private function existingSkill(int $skillId): Skill
    {
        $skill = Skill::query()->whereKey($skillId)->where('is_active', true)->lockForUpdate()->first();

        if ($skill === null) {
            throw ValidationException::withMessages(['skill_id' => 'กรุณาเลือกทักษะที่เปิดใช้งาน']);
        }

        return $skill;
    }

    private function findOrCreateSkill(string $name): Skill
    {
        $normalizedName = Skill::normalizeName($name);
        $skill = Skill::withTrashed()->where('normalized_name', $normalizedName)->lockForUpdate()->first();

        if ($skill === null) {
            try {
                $skill = Skill::query()->create([
                    'name' => $name,
                    'category' => 'ทั่วไป',
                    'is_active' => true,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                // การสร้างพร้อมกันอาจชน UNIQUE; อ่านแถวที่ชนะก่อนผูกทักษะกับนักศึกษา
                $skill = Skill::withTrashed()->where('normalized_name', $normalizedName)->lockForUpdate()->first();

                if ($skill === null) {
                    throw $exception;
                }
            }
        }

        if ($skill->trashed() || ! $skill->is_active) {
            throw ValidationException::withMessages(['skill_name' => 'ทักษะชื่อนี้ถูกปิดใช้งานหรือถูกลบแล้ว ไม่สามารถเพิ่มซ้ำได้']);
        }

        return $skill;
    }
}
