<?php

namespace App\Http\Requests;

use App\Models\Skill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserSkillRequest extends FormRequest
{
    private bool $invalidSkillName = false;

    public function authorize(): bool
    {
        return $this->user()?->role === 'student';
    }

    protected function prepareForValidation(): void
    {
        $skillName = $this->input('skill_name');

        if (is_string($skillName)) {
            $this->invalidSkillName = preg_match('/(?:(?!\s)\p{Cc}|\p{Cf}|[<>])/u', $skillName) !== 0;
            $this->merge(['skill_name' => Skill::cleanName($skillName)]);
        }
    }

    public function rules(): array
    {
        return [
            'skill_id' => [
                'required_without:skill_name',
                'prohibits:skill_name',
                'integer',
                Rule::exists('skills', 'id')->where('is_active', true)->whereNull('deleted_at'),
                Rule::unique('user_skills')->where(fn ($query) => $query
                    ->where('user_id', $this->user()->id)
                    ->where('skill_type', $this->input('skill_type'))),
            ],
            'skill_name' => [
                'required_without:skill_id',
                'prohibits:skill_id',
                'string',
                'max:255',
                function (string $attribute, mixed $value, $fail): void {
                    if ($this->invalidSkillName) {
                        $fail('ชื่อทักษะมีอักขระที่ไม่อนุญาต');
                    }
                },
            ],
            'skill_type' => ['required', Rule::in(['offered', 'wanted'])],
            'description' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['skill_id' => 'ทักษะ', 'skill_name' => 'ชื่อทักษะใหม่', 'skill_type' => 'ประเภททักษะ', 'description' => 'รายละเอียด'];
    }

    public function messages(): array
    {
        return [
            'skill_id.unique' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว',
            'skill_id.required_without' => 'กรุณาเลือกทักษะเดิมหรือกรอกชื่อทักษะใหม่',
            'skill_name.required_without' => 'กรุณาเลือกทักษะเดิมหรือกรอกชื่อทักษะใหม่',
            'skill_id.prohibits' => 'กรุณาเลือกทักษะเดิมหรือกรอกชื่อทักษะใหม่เพียงอย่างเดียว',
            'skill_name.prohibits' => 'กรุณาเลือกทักษะเดิมหรือกรอกชื่อทักษะใหม่เพียงอย่างเดียว',
            'skill_name.max' => 'ชื่อทักษะต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
