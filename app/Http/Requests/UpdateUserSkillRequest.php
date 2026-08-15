<?php

namespace App\Http\Requests;

use App\Models\UserSkill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'student';
    }

    public function rules(): array
    {
        /** @var UserSkill $userSkill */
        $userSkill = $this->route('userSkill');

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
