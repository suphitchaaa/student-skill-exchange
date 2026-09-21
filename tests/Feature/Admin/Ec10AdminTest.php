<?php

namespace Tests\Feature\Admin;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Ec10AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_case_1_admin_skill_crud_and_validation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.skills.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.skills.create'))->assertOk();
        $this->actingAs($admin)->post(route('admin.skills.store'), ['name' => '', 'category' => '', 'is_active' => 1])->assertSessionHasErrors(['name', 'category']);
        $this->actingAs($admin)->post(route('admin.skills.store'), ['name' => 'การถ่ายภาพ', 'category' => 'งานอดิเรก', 'is_active' => 1])->assertRedirect(route('admin.skills.index'));
        $skill = Skill::query()->where('name', 'การถ่ายภาพ')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.skills.edit', $skill))->assertOk();
        $this->actingAs($admin)->put(route('admin.skills.update', $skill), ['name' => 'การถ่ายภาพดิจิทัล', 'category' => 'งานอดิเรก', 'is_active' => 1])->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'การถ่ายภาพดิจิทัล']);
        $this->actingAs($admin)->delete(route('admin.skills.destroy', $skill))->assertRedirect(route('admin.skills.index'));
        $this->assertSoftDeleted('skills', ['id' => $skill->id]);
    }

    public function test_case_2_skill_availability_and_soft_delete_preserve_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create();
        $inactive = Skill::factory()->create(['is_active' => false]);
        $deleted = Skill::factory()->create();
        UserSkill::factory()->for($student)->for($deleted)->create(['skill_type' => 'offered']);
        $deleted->delete();
        $response = $this->actingAs($student)->get(route('user-skills.index'))->assertOk();
        $this->assertStringNotContainsString('<option value="'.$inactive->id.'"', $response->getContent());
        $this->assertStringNotContainsString('<option value="'.$deleted->id.'"', $response->getContent());
        $this->actingAs($student)->post(route('user-skills.store'), ['skill_id' => $inactive->id, 'skill_type' => 'offered'])->assertSessionHasErrors('skill_id');
        $this->actingAs($student)->post(route('user-skills.store'), ['skill_id' => $deleted->id, 'skill_type' => 'wanted'])->assertSessionHasErrors('skill_id');
        $this->assertDatabaseHas('user_skills', ['user_id' => $student->id, 'skill_id' => $deleted->id]);
        $this->assertFalse(collect(app('router')->getRoutes()->getRoutes())->contains(fn ($route) => str_contains($route->uri(), 'restore')));
        $this->assertTrue($admin->exists);
    }

    public function test_admin_create_rejects_normalized_duplicates_including_inactive_and_deleted_skills(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $active = Skill::factory()->create(['name' => 'Digital Art']);
        $inactive = Skill::factory()->create(['name' => 'Garden Design', 'is_active' => false]);
        $deleted = Skill::factory()->create(['name' => 'Paper Craft']);
        $deleted->delete();

        foreach (['  DIGITAL   ART  ', ' garden design ', 'PAPER CRAFT'] as $name) {
            $this->actingAs($admin)->post(route('admin.skills.store'), [
                'name' => $name,
                'category' => 'ทั่วไป',
                'is_active' => 1,
            ])->assertSessionHasErrors(['name' => 'ชื่อทักษะนี้มีอยู่แล้ว']);
        }

        $this->assertDatabaseCount('skills', 3);
        $this->assertSame('Digital Art', $active->fresh()->name);
        $this->assertSame('Garden Design', $inactive->fresh()->name);
        $this->assertSoftDeleted('skills', ['id' => $deleted->id]);
    }

    public function test_admin_rename_rejects_normalized_duplicate_and_allows_own_normalized_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $skill = Skill::factory()->create(['name' => 'Drawing Basics']);
        $other = Skill::factory()->create(['name' => 'Water Color']);

        $this->actingAs($admin)->put(route('admin.skills.update', $skill), [
            'name' => ' WATER   COLOR ',
            'category' => 'ศิลปะ',
            'is_active' => 0,
        ])->assertSessionHasErrors(['name' => 'ชื่อทักษะนี้มีอยู่แล้ว']);

        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'Drawing Basics', 'is_active' => 1]);
        $this->assertSame('Water Color', $other->fresh()->name);

        $this->actingAs($admin)->put(route('admin.skills.update', $skill), [
            'name' => ' DRAWING   BASICS ',
            'category' => 'ศิลปะ',
            'is_active' => 0,
        ])->assertRedirect(route('admin.skills.index'));

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'DRAWING BASICS',
            'normalized_name' => 'drawing basics',
            'category' => 'ศิลปะ',
            'is_active' => 0,
        ]);
    }

    public function test_student_created_skill_appears_in_admin_management(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($student)->post(route('user-skills.store'), [
            'skill_name' => ' Campus   Sketching ',
            'skill_type' => 'offered',
        ])->assertSessionHasNoErrors();

        $skill = Skill::query()->where('normalized_name', 'campus sketching')->firstOrFail();
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'category' => 'ทั่วไป', 'is_active' => 1]);
        $this->actingAs($admin)->get(route('admin.skills.index', ['q' => 'Campus']))
            ->assertOk()->assertSee('Campus Sketching')->assertSee('ทั่วไป');
        $this->actingAs($admin)->get(route('admin.skills.edit', $skill))->assertOk()->assertSee('Campus Sketching');
        $this->actingAs($admin)->put(route('admin.skills.update', $skill), [
            'name' => 'Campus Sketching',
            'category' => 'ศิลปะ',
            'is_active' => 0,
        ])->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'category' => 'ศิลปะ', 'is_active' => 0]);
    }

    public function test_completed_request_displays_skill_names_after_admin_soft_delete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$request] = $this->requestFixture('History Sender', 'History Receiver', 'completed', now());
        $senderSkill = $request->senderUserSkill->skill;
        $receiverSkill = $request->receiverUserSkill->skill;

        $this->actingAs($admin)->delete(route('admin.skills.destroy', $senderSkill))->assertRedirect(route('admin.skills.index'));
        $this->actingAs($admin)->delete(route('admin.skills.destroy', $receiverSkill))->assertRedirect(route('admin.skills.index'));

        $this->actingAs($admin)->get(route('admin.exchange-requests.index'))
            ->assertOk()->assertSee($senderSkill->name)->assertSee($receiverSkill->name);
        $this->actingAs($admin)->get(route('admin.exchange-requests.show', $request))
            ->assertOk()->assertSee($senderSkill->name)->assertSee($receiverSkill->name);
        $this->actingAs($request->sender)->get(route('exchange-requests.index', ['tab' => 'history']))
            ->assertOk()->assertSee($senderSkill->name)->assertSee($receiverSkill->name);
        $this->actingAs($request->receiver)->get(route('exchange-requests.show', $request))
            ->assertOk()->assertSee($senderSkill->name)->assertSee($receiverSkill->name);

        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'completed']);
        $this->assertDatabaseHas('user_skills', ['id' => $request->sender_user_skill_id]);
        $this->assertDatabaseHas('user_skills', ['id' => $request->receiver_user_skill_id]);
    }

    public function test_admin_request_monitor_existing_search_and_status_filters_continue_to_work(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$first] = $this->requestFixture('Searchable Sender', 'Receiver One', 'pending', now()->subDays(5));
        [$second] = $this->requestFixture('Other Sender', 'Receiver Two', 'accepted', now()->subDay());
        $this->actingAs($admin)->get(route('admin.exchange-requests.index'))->assertOk()->assertSee('Searchable Sender')->assertSee('Other Sender');
        $this->actingAs($admin)->get(route('admin.exchange-requests.index', ['q' => 'Searchable']))->assertSee('Searchable Sender')->assertDontSee('Other Sender');
        $this->actingAs($admin)->get(route('admin.exchange-requests.index', ['status' => 'accepted']))->assertSee('Other Sender')->assertDontSee('Searchable Sender');
        $this->assertNotSame($first->id, $second->id);
    }

    public function test_admin_request_monitor_searches_active_and_soft_deleted_skill_names(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$historicalRequest] = $this->requestFixture('Historical Skill Sender', 'Historical Skill Receiver', 'completed', now());
        [$activeRequest] = $this->requestFixture('Active Skill Sender', 'Active Skill Receiver', 'pending', now());
        $historicalSenderSkill = $historicalRequest->senderUserSkill->skill;
        $historicalReceiverSkill = $historicalRequest->receiverUserSkill->skill;

        $historicalSenderSkill->delete();
        $historicalReceiverSkill->delete();

        $this->actingAs($admin)->get(route('admin.exchange-requests.index', ['q' => $historicalSenderSkill->name]))
            ->assertOk()->assertSee('Historical Skill Sender')->assertDontSee('Active Skill Sender');
        $this->actingAs($admin)->get(route('admin.exchange-requests.index', ['q' => $historicalReceiverSkill->name]))
            ->assertOk()->assertSee('Historical Skill Receiver')->assertDontSee('Active Skill Receiver');
        $this->actingAs($admin)->get(route('admin.exchange-requests.index', ['q' => $activeRequest->senderUserSkill->skill->name]))
            ->assertOk()->assertSee('Active Skill Sender')->assertDontSee('Historical Skill Sender');
    }

    public function test_admin_request_monitor_rejects_invalid_date_from_without_server_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.exchange-requests.index'))
            ->get(route('admin.exchange-requests.index', ['date_from' => 'not-a-date']))
            ->assertRedirect(route('admin.exchange-requests.index'))
            ->assertSessionHasErrors(['date_from' => 'วันที่เริ่มต้นต้องเป็นวันที่ที่ถูกต้อง']);
    }

    public function test_admin_request_monitor_rejects_invalid_date_to_without_server_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.exchange-requests.index'))
            ->get(route('admin.exchange-requests.index', ['date_to' => 'not-a-date']))
            ->assertRedirect(route('admin.exchange-requests.index'))
            ->assertSessionHasErrors(['date_to' => 'วันที่สิ้นสุดต้องเป็นวันที่ที่ถูกต้อง']);
    }

    public function test_admin_request_monitor_valid_date_filtering_still_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->requestFixture('Outside Date Range', 'Receiver One', 'pending', now()->subDays(5));
        $this->requestFixture('Inside Date Range', 'Receiver Two', 'accepted', now()->subDay());

        $this->actingAs($admin)->get(route('admin.exchange-requests.index', [
            'date_from' => now()->subDays(2)->toDateString(),
            'date_to' => now()->toDateString(),
        ]))->assertOk()->assertSee('Inside Date Range')->assertDontSee('Outside Date Range');
    }

    public function test_admin_request_monitor_rejects_reversed_date_range(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('admin.exchange-requests.index'))
            ->get(route('admin.exchange-requests.index', [
                'date_from' => '2026-09-21',
                'date_to' => '2026-09-20',
            ]))
            ->assertRedirect(route('admin.exchange-requests.index'))
            ->assertSessionHasErrors(['date_to' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น']);
    }

    public function test_case_4_admin_request_detail_is_read_only_and_shows_full_values(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$request] = $this->requestFixture('Detail Sender', 'Detail Receiver', 'completed', now(), str_repeat('ข้อความยาว ', 20));
        $this->actingAs($admin)->get(route('admin.exchange-requests.show', $request))->assertOk()->assertSee('Detail Sender')->assertSee('Detail Receiver')->assertSee($request->senderUserSkill->skill->name)->assertSee($request->receiverUserSkill->skill->name)->assertSee($request->message)->assertDontSee('ยอมรับ')->assertDontSee('ปฏิเสธ')->assertDontSee('ยกเลิกคำขอ');
    }

    public function test_case_5_admin_cannot_change_request_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$request] = $this->requestFixture('State Sender', 'State Receiver', 'pending', now());
        $adminRouteNames = collect(app('router')->getRoutes()->getRoutes())->map(fn ($route) => $route->getName())->filter(fn ($name) => is_string($name));
        foreach (['accept', 'reject', 'cancel', 'complete'] as $transition) {
            $this->assertFalse($adminRouteNames->contains(fn ($name) => str_starts_with($name, 'admin.exchange-requests.'.$transition)));
        }
        $this->actingAs($admin)->patch(route('exchange-requests.accept', $request))->assertForbidden();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'pending']);
    }

    public function test_case_6_role_isolation(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($student)->get(route('admin.skills.index'))->assertForbidden();
        $this->actingAs($student)->get(route('admin.exchange-requests.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('student.dashboard'))->assertForbidden();
    }

    private function requestFixture(string $senderName, string $receiverName, string $status, $createdAt, ?string $message = null): array
    {
        $sender = User::factory()->create(['name' => $senderName]);
        $receiver = User::factory()->create(['name' => $receiverName]);
        $senderSkill = UserSkill::factory()->for($sender)->for(Skill::factory()->create(['name' => $senderName.' Skill']))->create(['skill_type' => 'offered']);
        $receiverSkill = UserSkill::factory()->for($receiver)->for(Skill::factory()->create(['name' => $receiverName.' Skill']))->create(['skill_type' => 'offered']);
        $request = ExchangeRequest::factory()->create(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'sender_user_skill_id' => $senderSkill->id, 'receiver_user_skill_id' => $receiverSkill->id, 'status' => $status, 'message' => $message ?? 'ข้อความคำขอ']);
        $request->forceFill(['created_at' => $createdAt])->saveQuietly();

        return [$request->fresh(['senderUserSkill.skill', 'receiverUserSkill.skill'])];
    }
}
