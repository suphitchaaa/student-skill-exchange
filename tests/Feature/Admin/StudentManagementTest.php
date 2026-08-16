<?php

namespace Tests\Feature\Admin;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_real_summary_data_and_recent_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'student', 'status' => 'active']);
        User::factory()->create(['role' => 'student', 'status' => 'suspended']);
        Skill::factory()->create();
        Skill::factory()->create(['is_active' => false]);
        $deletedSkill = Skill::factory()->create();
        $deletedSkill->delete();
        ExchangeRequest::factory()->create(['status' => 'pending']);
        $studentCount = User::query()->where('role', 'student')->count();
        $activeStudentCount = User::query()->where('role', 'student')->where('status', 'active')->count();
        $suspendedStudentCount = User::query()->where('role', 'student')->where('status', 'suspended')->count();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('studentsCount', $studentCount)
            ->assertViewHas('activeStudentsCount', $activeStudentCount)
            ->assertViewHas('suspendedStudentsCount', $suspendedStudentCount)
            ->assertViewHas('skillsCount', 3)
            ->assertSee('คำขอล่าสุด');
    }

    public function test_admin_student_list_searches_students_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $match = User::factory()->create(['name' => 'Student Match', 'role' => 'student']);
        StudentProfile::factory()->create(['user_id' => $match->id, 'faculty' => 'Engineering']);
        User::factory()->create(['name' => 'Student Other', 'role' => 'student']);
        User::factory()->create(['name' => 'Admin Match', 'role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.students.index', ['q' => 'Match']))
            ->assertOk()
            ->assertSee('Student Match')
            ->assertDontSee('Student Other')
            ->assertDontSee('Admin Match');
    }

    public function test_admin_can_suspend_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($admin)->patch(route('admin.students.suspend', $student))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $student->id, 'role' => 'student', 'status' => 'suspended']);
    }

    public function test_admin_can_activate_suspended_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'suspended']);

        $this->actingAs($admin)->patch(route('admin.students.activate', $student))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $student->id, 'role' => 'student', 'status' => 'active']);
    }

    public function test_suspension_blocks_an_existing_authenticated_student_session(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($student);
        $student->update(['status' => 'suspended']);

        $this->get(route('student.dashboard'))->assertRedirectToRoute('account.suspended');
        $this->get(route('account.suspended'))->assertOk();
        $this->assertAuthenticatedAs($student->fresh());
    }

    public function test_admin_student_management_is_role_isolated(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($student)->get(route('admin.students.index'))->assertForbidden();
        $this->actingAs($student)->patch(route('admin.students.suspend', $admin))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.students.show', $admin))->assertNotFound();
    }
}
