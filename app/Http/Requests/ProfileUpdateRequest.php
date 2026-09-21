<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'student';
    }

    public function rules(): array
    {
        return [
            'faculty' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'integer', 'between:1,8'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'contact_channel' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'faculty' => 'คณะ',
            'major' => 'สาขาวิชา',
            'year_level' => 'ชั้นปี',
            'bio' => 'แนะนำตัว',
            'phone' => 'เบอร์โทรศัพท์',
            'contact_channel' => 'ช่องทางติดต่อ',
        ];
    }

    public function messages(): array
    {
        return [
            'faculty.max' => 'คณะต้องไม่เกิน 255 ตัวอักษร',
            'major.max' => 'สาขาวิชาต้องไม่เกิน 255 ตัวอักษร',
            'phone.max' => 'เบอร์โทรศัพท์ต้องไม่เกิน 255 ตัวอักษร',
            'contact_channel.max' => 'ช่องทางติดต่อต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
