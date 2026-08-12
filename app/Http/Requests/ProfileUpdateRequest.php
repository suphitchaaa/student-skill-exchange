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
            'faculty' => ['nullable', 'string'],
            'major' => ['nullable', 'string'],
            'year_level' => ['nullable', 'integer', 'between:1,8'],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'contact_channel' => ['nullable', 'string'],
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
}
