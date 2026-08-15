<?php

namespace Tests\Feature\Search;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_student_is_excluded_from_search_results(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $other = $this->student('นักศึกษาคนอื่น');

        $this->actingAs($current)->get(route('students.index'))->assertOk()
            ->assertSee($other->name)
            ->assertDontSee('href="'.route('students.show', $current).'"', false);
    }

    public function test_suspended_inactive_and_admin_users_are_excluded(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $this->student('นักศึกษาที่ใช้งานได้');
        $this->student('นักศึกษาที่ระงับ', 'suspended');
        $this->student('นักศึกษาที่ไม่ใช้งาน', 'inactive');
        User::factory()->create(['name' => 'ผู้ดูแลระบบ', 'role' => 'admin', 'status' => 'active']);

        $response = $this->actingAs($current)->get(route('students.index'));

        $response->assertSee('นักศึกษาที่ใช้งานได้')
            ->assertDontSee('นักศึกษาที่ระงับ')
            ->assertDontSee('นักศึกษาที่ไม่ใช้งาน')
            ->assertDontSee('ผู้ดูแลระบบ');
    }

    public function test_student_name_search_works(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $this->student('สมชาย ใจดี');
        $this->student('สุดา มั่นคง');

        $this->actingAs($current)->get(route('students.index', ['search' => 'สมชาย']))
            ->assertSee('สมชาย ใจดี')
            ->assertDontSee('สุดา มั่นคง');
    }

    public function test_skill_faculty_and_year_filters_work_without_duplicate_students(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $skill = Skill::factory()->create(['name' => 'การวาดภาพ']);
        $matching = $this->student('นักศึกษาตรงเงื่อนไข');
        $matching->studentProfile()->update(['faculty' => 'ศิลปกรรมศาสตร์', 'year_level' => 3]);
        $matching->userSkills()->create(['skill_id' => $skill->id, 'skill_type' => 'offered']);
        $other = $this->student('นักศึกษาคนละเงื่อนไข');
        $other->studentProfile()->update(['faculty' => 'วิทยาศาสตร์', 'year_level' => 2]);
        $other->userSkills()->create(['skill_id' => $skill->id, 'skill_type' => 'wanted']);

        $response = $this->actingAs($current)->get(route('students.index', [
            'skill_id' => $skill->id,
            'faculty' => 'ศิลปกรรมศาสตร์',
            'year_level' => 3,
        ]));

        $response->assertSee('นักศึกษาตรงเงื่อนไข')
            ->assertDontSee('นักศึกษาคนละเงื่อนไข')
            ->assertSee('การวาดภาพ');
        $this->assertSame(1, substr_count($response->getContent(), 'นักศึกษาตรงเงื่อนไข'));
    }

    public function test_empty_state_has_working_clear_filter_action(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $this->student('นักศึกษาที่มีอยู่');

        $this->actingAs($current)->get(route('students.index', ['search' => 'ไม่มีชื่อนี้']))
            ->assertOk()
            ->assertSee('ไม่พบนักศึกษาตามเงื่อนไข')
            ->assertSee('href="'.route('students.index').'"', false);
    }

    public function test_public_profile_shows_public_profile_and_skill_information(): void
    {
        $current = $this->student('ผู้ค้นหา');
        $student = $this->student('นักศึกษาสาธารณะ');
        $student->studentProfile()->update([
            'faculty' => 'วิศวกรรมศาสตร์',
            'major' => 'คอมพิวเตอร์',
            'year_level' => 4,
            'bio' => 'ชอบแบ่งปันความรู้',
            'phone' => '0812345678',
            'contact_channel' => 'private-contact',
        ]);
        $skill = Skill::factory()->create(['name' => 'ภาษาอังกฤษ']);
        $student->userSkills()->create(['skill_id' => $skill->id, 'skill_type' => 'offered', 'description' => 'สนทนาได้']);

        $this->actingAs($current)->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('นักศึกษาสาธารณะ')
            ->assertSee('วิศวกรรมศาสตร์')
            ->assertSee('ภาษาอังกฤษ')
            ->assertDontSee('0812345678')
            ->assertDontSee('private-contact');
    }

    private function student(string $name, string $status = 'active'): User
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'student', 'status' => $status]);
        $user->studentProfile()->create();

        return $user;
    }
}
