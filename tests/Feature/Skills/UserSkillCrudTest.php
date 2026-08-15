<?php

namespace Tests\Feature\Skills;

use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSkillCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_create_an_offered_skill(): void
    {
        $student = $this->student();
        $skill = Skill::factory()->create();

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_id' => $skill->id,
            'skill_type' => 'offered',
            'description' => 'สอนพื้นฐานได้',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'offered']);

        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $skill->id, 'skill_type' => 'offered']);
    }

    public function test_student_can_create_a_wanted_skill(): void
    {
        $student = $this->student();
        $skill = Skill::factory()->create();

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_id' => $skill->id,
            'skill_type' => 'wanted',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'wanted']);

        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $skill->id, 'skill_type' => 'wanted']);
    }

    public function test_student_can_edit_own_skill(): void
    {
        $student = $this->student();
        $userSkill = UserSkill::factory()->for($student)->create(['skill_type' => 'offered']);

        $this->actingAs($student)->put(route('user-skills.update', $userSkill), [
            'skill_id' => $userSkill->skill_id,
            'skill_type' => 'offered',
            'description' => 'แก้ไขรายละเอียดแล้ว',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'offered']);

        $this->assertDatabaseHas('user_skills', ['id' => $userSkill->id, 'description' => 'แก้ไขรายละเอียดแล้ว']);
    }

    public function test_student_can_delete_own_skill(): void
    {
        $student = $this->student();
        $userSkill = UserSkill::factory()->for($student)->create();

        $this->actingAs($student)->delete(route('user-skills.destroy', $userSkill))
            ->assertRedirectToRoute('user-skills.index', ['type' => $userSkill->skill_type]);

        $this->assertDatabaseMissing('user_skills', ['id' => $userSkill->id]);
    }

    public function test_duplicate_skill_and_type_is_rejected_with_thai_feedback(): void
    {
        $student = $this->student();
        $skill = Skill::factory()->create();
        UserSkill::factory()->for($student)->for($skill)->create(['skill_type' => 'offered']);

        $this->from(route('user-skills.index'))->actingAs($student)->post(route('user-skills.store'), [
            'skill_id' => $skill->id,
            'skill_type' => 'offered',
        ])->assertRedirect(route('user-skills.index'))
            ->assertSessionHasErrors(['skill_id' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว']);
    }

    public function test_inactive_or_soft_deleted_skill_cannot_be_created(): void
    {
        $student = $this->student();
        $inactiveSkill = Skill::factory()->create(['is_active' => false]);
        $deletedSkill = Skill::factory()->create();
        $deletedSkill->delete();

        foreach ([$inactiveSkill, $deletedSkill] as $skill) {
            $this->actingAs($student)->post(route('user-skills.store'), [
                'skill_id' => $skill->id,
                'skill_type' => 'wanted',
            ])->assertSessionHasErrors('skill_id');
        }

        $this->assertDatabaseCount('user_skills', 0);
    }

    public function test_student_cannot_edit_or_delete_another_students_skill(): void
    {
        $student = $this->student();
        $otherUserSkill = UserSkill::factory()->create();

        $this->actingAs($student)->put(route('user-skills.update', $otherUserSkill), [
            'skill_id' => $otherUserSkill->skill_id,
            'skill_type' => $otherUserSkill->skill_type,
        ])->assertForbidden();

        $this->actingAs($student)->delete(route('user-skills.destroy', $otherUserSkill))->assertForbidden();
    }

    private function student(): User
    {
        return User::factory()->create(['role' => 'student', 'status' => 'active']);
    }
}
