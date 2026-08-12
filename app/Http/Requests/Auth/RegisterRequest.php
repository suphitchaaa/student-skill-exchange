<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'student_code' => ['required', 'string', 'max:255', 'unique:users,student_code'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'student_code.unique' => 'รหัสนักศึกษานี้ถูกใช้งานแล้ว',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ];
    }
}
