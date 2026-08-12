<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'student';
    }

    public function rules(): array
    {
        return [
            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'mimetypes:image/jpeg,image/png,image/webp',
                'extensions:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function attributes(): array
    {
        return ['profile_image' => 'รูปโปรไฟล์'];
    }

    public function messages(): array
    {
        return [
            'profile_image.image' => 'รูปโปรไฟล์ต้องเป็นไฟล์รูปภาพจริง',
            'profile_image.mimes' => 'รูปโปรไฟล์ต้องเป็นไฟล์ JPG, JPEG, PNG หรือ WEBP',
            'profile_image.mimetypes' => 'รูปโปรไฟล์ต้องเป็นไฟล์ JPG, JPEG, PNG หรือ WEBP',
            'profile_image.extensions' => 'รูปโปรไฟล์ต้องมีนามสกุล .jpg, .jpeg, .png หรือ .webp',
            'profile_image.max' => 'รูปโปรไฟล์ต้องมีขนาดไม่เกิน 2 MB',
        ];
    }
}
