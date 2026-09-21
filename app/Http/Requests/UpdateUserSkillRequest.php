<?php

namespace App\Http\Requests;

use App\Models\UserSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userSkill = $this->route('userSkill');

        return $userSkill instanceof UserSkill
            && ($this->user()?->can('update', $userSkill) ?? false);
    }

    public function rules(): array
    {
        /** @var UserSkill $userSkill */
        $userSkill = $this->route('userSkill');

        if ($userSkill->isReferencedByExchangeRequest()) {
            return [
                'skill_id' => [
                    'required',
                    'integer',
                    function (string $attribute, mixed $value, $fail) use ($userSkill): void {
                        if ((int) $value !== $userSkill->skill_id) {
                            $fail(UserSkill::REFERENCED_SKILL_LOCKED_MESSAGE);
                        }
                    },
                ],
                'skill_type' => [
                    'required',
                    function (string $attribute, mixed $value, $fail) use ($userSkill): void {
                        if ($value !== $userSkill->skill_type) {
                            $fail(UserSkill::REFERENCED_TYPE_LOCKED_MESSAGE);
                        }
                    },
                ],
                'description' => ['nullable', 'string'],
            ];
        }

        return [
            'skill_id' => [
                'required',
                'integer',
                Rule::exists('skills', 'id')->where('is_active', true)->whereNull('deleted_at'),
                Rule::unique('user_skills')->ignore($userSkill)->where(fn ($query) => $query
                    ->where('user_id', $this->user()->id)
                    ->where('skill_type', $this->input('skill_type'))),
            ],
            'skill_type' => ['required', Rule::in(['offered', 'wanted'])],
            'description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['skill_id' => 'ทักษะ', 'skill_type' => 'ประเภททักษะ', 'description' => 'รายละเอียด'];
    }

    public function messages(): array
    {
        return ['skill_id.unique' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว'];
    }
}
