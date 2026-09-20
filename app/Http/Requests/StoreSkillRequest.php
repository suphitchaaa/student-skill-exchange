<?php

namespace App\Http\Requests;

use App\Models\Skill;
use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge(['name' => Skill::cleanName($this->input('name'))]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', function (string $attribute, mixed $value, $fail): void {
                if (! is_string($value) || $value === '') {
                    return;
                }

                // ตรวจชื่อที่ normalize แล้วกับทักษะทุกสถานะ รวมรายการที่ลบแบบ soft delete
                $matchingSkill = Skill::query()->withTrashed()->where('normalized_name', Skill::normalizeName($value));
                $currentSkill = $this->route('skill');
                if ($currentSkill instanceof Skill) {
                    $matchingSkill->whereKeyNot($currentSkill->getKey());
                }

                if ($matchingSkill->exists()) {
                    $fail('ชื่อทักษะนี้มีอยู่แล้ว');
                }
            }],
            'category' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'ชื่อทักษะ', 'category' => 'หมวดหมู่', 'is_active' => 'สถานะการใช้งาน'];
    }

    public function messages(): array
    {
        return ['required' => 'กรุณากรอก:attribute', 'string' => ':attribute ต้องเป็นข้อความ', 'boolean' => ':attribute ไม่ถูกต้อง'];
    }
}
