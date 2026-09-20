<?php

namespace Tests\Feature\Skills;

use App\Models\ExchangeRequest;
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

    public function test_add_form_renders_search_and_only_active_skill_suggestions_on_both_tabs(): void
    {
        $student = $this->student();
        $active = Skill::factory()->create(['name' => 'Canva', 'category' => 'การออกแบบ']);
        $inactive = Skill::factory()->create(['is_active' => false]);
        $deleted = Skill::factory()->create();
        $deleted->delete();

        foreach (['offered', 'wanted'] as $type) {
            $this->actingAs($student)->get(route('user-skills.index', ['type' => $type]))
                ->assertOk()
                ->assertSee('placeholder="ค้นหาหรือเพิ่มทักษะ..."', false)
                ->assertSee('role="combobox"', false)
                ->assertSee('name="skill_id" type="hidden"', false)
                ->assertSee('name="skill_name" type="hidden"', false)
                ->assertSee('data-skill-id="'.$active->id.'"', false)
                ->assertSee('การออกแบบ')
                ->assertDontSee('data-skill-id="'.$inactive->id.'"', false)
                ->assertDontSee('data-skill-id="'.$deleted->id.'"', false);
        }
    }

    public function test_add_form_preserves_new_name_after_validation_error(): void
    {
        $student = $this->student();
        Skill::factory()->create(['name' => 'Canva', 'is_active' => false]);

        $this->from(route('user-skills.index', ['type' => 'wanted']))->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => ' CANVA ',
            'skill_type' => 'wanted',
        ])->assertSessionHasErrors('skill_name');

        $this->actingAs($student)->get(route('user-skills.index', ['type' => 'wanted']))
            ->assertSee('id="skill_search" type="search" value="CANVA"', false)
            ->assertSee('ทักษะชื่อนี้ถูกปิดใช้งานหรือถูกลบแล้ว ไม่สามารถเพิ่มซ้ำได้');
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

    public function test_student_can_create_a_new_offered_skill_immediately(): void
    {
        $student = $this->student();

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => "  Canva\tDesign  ",
            'skill_type' => 'offered',
            'description' => 'สอนพื้นฐานได้',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'offered'])
            ->assertSessionHas('success', 'เพิ่มทักษะเรียบร้อยแล้ว');

        $skill = Skill::query()->where('normalized_name', 'canva design')->firstOrFail();
        $this->assertSame('Canva Design', $skill->name);
        $this->assertSame('ทั่วไป', $skill->category);
        $this->assertTrue($skill->is_active);
        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $skill->id, 'skill_type' => 'offered', 'description' => 'สอนพื้นฐานได้']);
    }

    public function test_student_can_create_a_new_wanted_skill_immediately(): void
    {
        $student = $this->student();

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => 'ภาษาเกาหลี',
            'skill_type' => 'wanted',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'wanted']);

        $skill = Skill::query()->where('normalized_name', 'ภาษาเกาหลี')->firstOrFail();
        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $skill->id, 'skill_type' => 'wanted']);
    }

    public function test_new_name_reuses_an_active_skill_with_the_same_normalized_name(): void
    {
        $student = $this->student();
        $skill = Skill::factory()->create(['name' => 'Canva']);

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => "\u{00A0} cAnVa \t",
            'skill_type' => 'wanted',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'wanted']);

        $this->assertDatabaseCount('skills', 1);
        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $skill->id, 'skill_type' => 'wanted']);
    }

    public function test_new_skill_is_available_to_other_students_and_both_skill_types(): void
    {
        $creator = $this->student();
        $otherStudent = $this->student();

        $this->actingAs($creator)->post(route('user-skills.store'), [
            'skill_name' => 'Canva',
            'skill_type' => 'offered',
        ])->assertRedirectToRoute('user-skills.index', ['type' => 'offered']);

        $skill = Skill::query()->where('normalized_name', 'canva')->firstOrFail();
        $this->actingAs($otherStudent)->get(route('user-skills.index'))->assertViewHas('availableSkills', fn ($skills) => $skills->contains('id', $skill->id));

        foreach ([$creator, $otherStudent] as $student) {
            $this->actingAs($student)->post(route('user-skills.store'), [
                'skill_name' => ' CANVA ',
                'skill_type' => 'wanted',
            ])->assertRedirectToRoute('user-skills.index', ['type' => 'wanted']);
        }

        $this->assertDatabaseCount('skills', 1);
        $this->assertDatabaseHas('user_skills', ['user_id' => $creator->id, 'skill_id' => $skill->id, 'skill_type' => 'offered']);
        $this->assertDatabaseHas('user_skills', ['user_id' => $creator->id, 'skill_id' => $skill->id, 'skill_type' => 'wanted']);
        $this->assertDatabaseHas('user_skills', ['user_id' => $otherStudent->id, 'skill_id' => $skill->id, 'skill_type' => 'wanted']);
    }

    public function test_new_name_cannot_duplicate_a_personal_skill_of_the_same_type(): void
    {
        $student = $this->student();
        $skill = Skill::factory()->create(['name' => 'Canva']);
        UserSkill::factory()->for($student)->for($skill)->create(['skill_type' => 'offered']);

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => ' CANVA ',
            'skill_type' => 'offered',
        ])->assertSessionHasErrors(['skill_name' => 'คุณเพิ่มทักษะนี้ในประเภทที่เลือกไว้แล้ว']);

        $this->assertDatabaseCount('skills', 1);
        $this->assertDatabaseCount('user_skills', 1);
    }

    public function test_new_name_cannot_recreate_an_inactive_or_soft_deleted_skill(): void
    {
        $student = $this->student();
        $inactiveSkill = Skill::factory()->create(['name' => 'Canva', 'is_active' => false]);
        $deletedSkill = Skill::factory()->create(['name' => 'Microsoft Excel']);
        $deletedSkill->delete();

        foreach ([' canva ' => $inactiveSkill, 'MICROSOFT EXCEL' => $deletedSkill] as $name => $skill) {
            $this->actingAs($student)->post(route('user-skills.store'), [
                'skill_name' => $name,
                'skill_type' => 'offered',
            ])->assertSessionHasErrors('skill_name');

            $this->assertDatabaseHas('skills', ['id' => $skill->id, 'is_active' => $skill->is_active]);
        }

        $this->assertSoftDeleted('skills', ['id' => $deletedSkill->id]);
        $this->assertDatabaseCount('skills', 2);
        $this->assertDatabaseCount('user_skills', 0);
    }

    public function test_new_skill_name_rejects_invalid_text_and_requires_one_input(): void
    {
        $student = $this->student();

        foreach ([" \u{00A0}\t ", '<script>alert(1)</script>', "Can\0va", "Can\u{200B}va", str_repeat('x', 256)] as $name) {
            $this->actingAs($student)->post(route('user-skills.store'), [
                'skill_name' => $name,
                'skill_type' => 'offered',
            ])->assertSessionHasErrors('skill_name');
        }

        $skill = Skill::factory()->create();
        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_id' => $skill->id,
            'skill_name' => 'Canva',
            'skill_type' => 'offered',
        ])->assertSessionHasErrors(['skill_id', 'skill_name']);
        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_type' => 'offered',
        ])->assertSessionHasErrors(['skill_id', 'skill_name']);

        $this->assertDatabaseCount('skills', 1);
        $this->assertDatabaseCount('user_skills', 0);
    }

    public function test_new_skill_creation_keeps_guest_admin_and_suspended_access_rules(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $suspended = User::factory()->create(['role' => 'student', 'status' => 'suspended']);
        $data = ['skill_name' => 'Canva', 'skill_type' => 'offered'];

        $this->post(route('user-skills.store'), $data)->assertRedirect(route('login'));
        $this->actingAs($admin)->post(route('user-skills.store'), $data)->assertForbidden();
        $this->actingAs($suspended)->post(route('user-skills.store'), $data)->assertRedirect(route('account.suspended'));

        $this->assertDatabaseCount('skills', 0);
        $this->assertDatabaseCount('user_skills', 0);
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
            ->assertRedirectToRoute('user-skills.index', ['type' => $userSkill->skill_type])
            ->assertSessionHas('success', 'ลบทักษะเรียบร้อยแล้ว')
            ->assertSessionMissing('error');

        $this->assertDatabaseMissing('user_skills', ['id' => $userSkill->id]);
    }

    public function test_student_cannot_delete_skill_referenced_as_sender(): void
    {
        $student = $this->student();
        $receiver = $this->student();
        $userSkill = UserSkill::factory()->for($student)->create(['skill_type' => 'offered']);
        $receiverSkill = UserSkill::factory()->for($receiver)->create(['skill_type' => 'offered']);
        $exchangeRequest = ExchangeRequest::factory()->create([
            'sender_id' => $student->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $userSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
        ]);

        $this->actingAs($student)->delete(route('user-skills.destroy', $userSkill))
            ->assertRedirectToRoute('user-skills.index', ['type' => 'offered'])
            ->assertSessionHas('error', 'ไม่สามารถลบทักษะนี้ได้ เนื่องจากถูกใช้ในคำขอแลกเปลี่ยนหรือประวัติแล้ว')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_skills', ['id' => $userSkill->id]);
        $this->assertDatabaseHas('exchange_requests', ['id' => $exchangeRequest->id, 'sender_user_skill_id' => $userSkill->id]);
        $this->actingAs($student)->get(route('user-skills.index', ['type' => 'offered']))
            ->assertOk()
            ->assertSee('<div class="alert alert-danger" role="alert">ไม่สามารถลบทักษะนี้ได้ เนื่องจากถูกใช้ในคำขอแลกเปลี่ยนหรือประวัติแล้ว</div>', false);
    }

    public function test_student_cannot_delete_skill_referenced_as_receiver_in_history(): void
    {
        $sender = $this->student();
        $student = $this->student();
        $senderSkill = UserSkill::factory()->for($sender)->create(['skill_type' => 'offered']);
        $userSkill = UserSkill::factory()->for($student)->create(['skill_type' => 'wanted']);
        $exchangeRequest = ExchangeRequest::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $student->id,
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $userSkill->id,
            'status' => 'completed',
        ]);

        $this->actingAs($student)->delete(route('user-skills.destroy', $userSkill))
            ->assertRedirectToRoute('user-skills.index', ['type' => 'wanted'])
            ->assertSessionHas('error', 'ไม่สามารถลบทักษะนี้ได้ เนื่องจากถูกใช้ในคำขอแลกเปลี่ยนหรือประวัติแล้ว')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_skills', ['id' => $userSkill->id]);
        $this->assertDatabaseHas('exchange_requests', ['id' => $exchangeRequest->id, 'receiver_user_skill_id' => $userSkill->id, 'status' => 'completed']);
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
