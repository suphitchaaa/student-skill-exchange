<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
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
