<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'student' && $this->user()?->status === 'active';
    }

    public function rules(): array
    {
        return [
            'sender_user_skill_id' => ['required', 'integer'],
            'receiver_user_skill_id' => ['required', 'integer'],
            'learning_format' => ['required', Rule::in(['online', 'onsite', 'either'])],
            'preferred_schedule' => ['required', 'string'],
            'message' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'sender_user_skill_id' => 'ทักษะที่คุณสอนได้',
            'receiver_user_skill_id' => 'ทักษะของผู้รับ',
            'learning_format' => 'รูปแบบการเรียนรู้',
            'preferred_schedule' => 'ช่วงเวลาที่สะดวก',
            'message' => 'ข้อความ',
        ];
    }
}
