<?php

namespace Database\Seeders;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['เทคโนโลยี', 'ภาษา', 'การออกแบบ', 'ธุรกิจ', 'วิชาการ', 'งานอดิเรก'];
        $skills = collect($categories)->mapWithKeys(fn (string $category, int $index) => [
            $category => Skill::query()->create(['name' => 'ทักษะสาธิต '.($index + 1), 'category' => $category, 'is_active' => true]),
        ]);

        $students = collect(range(1, 12))->map(function (int $number) use ($skills): User {
            $student = User::factory()->create([
                'name' => 'นักศึกษาสาธิต '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'student_code' => 'DEMO'.str_pad((string) $number, 4, '0', STR_PAD_LEFT),
                'email' => 'demo.student'.$number.'@example.com',
            ]);
            StudentProfile::factory()->create(['user_id' => $student->id, 'faculty' => 'คณะสาธิต', 'major' => 'สาขาสหวิทยาการ', 'year_level' => (($number - 1) % 4) + 1]);
            $student->userSkills()->createMany([
                ['skill_id' => $skills->values()->get(($number - 1) % $skills->count())->id, 'skill_type' => 'offered', 'description' => 'ทักษะสำหรับข้อมูลสาธิต'],
                ['skill_id' => $skills->values()->get($number % $skills->count())->id, 'skill_type' => 'wanted', 'description' => 'ทักษะที่ต้องการเรียนรู้'],
            ]);

            return $student->load('userSkills');
        });

        foreach (['pending', 'accepted', 'rejected', 'cancelled', 'completed'] as $index => $status) {
            $sender = $students[$index];
            $receiver = $students[$index + 1];
            $request = ['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'sender_user_skill_id' => $sender->userSkills->firstWhere('skill_type', 'offered')->id, 'receiver_user_skill_id' => $receiver->userSkills->firstWhere('skill_type', 'offered')->id, 'learning_format' => ['online', 'onsite', 'either', 'online', 'onsite'][$index], 'preferred_schedule' => 'วันเสาร์ เวลา '.(9 + $index).':00 น.', 'message' => 'ข้อความคำขอสาธิตสำหรับสถานะ '.$status, 'status' => $status];
            if (in_array($status, ['accepted', 'rejected', 'cancelled', 'completed'], true)) {
                $request['responded_at'] = now()->subDays(5 - $index);
            }
            if ($status === 'completed') {
                $request['completed_at'] = now()->subDay();
            }
            ExchangeRequest::withoutEvents(fn () => ExchangeRequest::query()->create($request));
        }
    }
}
