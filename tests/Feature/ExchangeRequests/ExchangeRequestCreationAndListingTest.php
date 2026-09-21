<?php

namespace Tests\Feature\ExchangeRequests;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeRequestCreationAndListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_create_a_pending_exchange_request(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $receiverSkill))
            ->assertRedirectToRoute('exchange-requests.index', ['tab' => 'sent']);

        $this->assertDatabaseHas('exchange_requests', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
            'status' => 'pending',
        ]);
    }

    public function test_required_fields_and_learning_format_are_validated(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), [
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
        ])->assertSessionHasErrors(['learning_format', 'preferred_schedule', 'message']);

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $receiverSkill, ['learning_format' => 'offline']))
            ->assertSessionHasErrors('learning_format');
    }

    public function test_preferred_schedule_accepts_255_characters(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        $boundaryValue = str_repeat('a', 255);

        $this->actingAs($sender)->post(
            route('exchange-requests.store', $receiver),
            $this->payload($senderSkill, $receiverSkill, ['preferred_schedule' => $boundaryValue]),
        )->assertRedirectToRoute('exchange-requests.index', ['tab' => 'sent'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('exchange_requests', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'preferred_schedule' => $boundaryValue,
        ]);
    }

    public function test_preferred_schedule_rejects_256_characters_before_database_insert(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();

        $this->actingAs($sender)->post(
            route('exchange-requests.store', $receiver),
            $this->payload($senderSkill, $receiverSkill, ['preferred_schedule' => str_repeat('a', 256)]),
        )->assertSessionHasErrors('preferred_schedule');

        $this->assertDatabaseCount('exchange_requests', 0);
    }

    public function test_student_cannot_send_a_request_to_self(): void
    {
        $student = $this->student('นักศึกษาผู้ส่ง');
        $skill = $this->offeredSkill($student, 'ทักษะของฉัน');

        $this->actingAs($student)->post(route('exchange-requests.store', $student), $this->payload($skill, $skill))
            ->assertNotFound();
    }

    public function test_request_to_an_inactive_receiver_is_rejected(): void
    {
        $sender = $this->student('ผู้ส่ง');
        $receiver = $this->student('ผู้รับ', 'suspended');
        $senderSkill = $this->offeredSkill($sender, 'ทักษะผู้ส่ง');
        $receiverSkill = $this->offeredSkill($receiver, 'ทักษะผู้รับ');

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $receiverSkill))
            ->assertNotFound();
    }

    public function test_sender_skill_must_belong_to_sender(): void
    {
        [$sender, $receiver, , $receiverSkill] = $this->exchangePair();
        $other = $this->student('เจ้าของทักษะคนอื่น');
        $otherSkill = $this->offeredSkill($other, 'ทักษะของคนอื่น');

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($otherSkill, $receiverSkill))
            ->assertSessionHasErrors('sender_user_skill_id');
    }

    public function test_sender_skill_must_be_offered(): void
    {
        [$sender, $receiver, , $receiverSkill] = $this->exchangePair();
        $wantedSkill = UserSkill::factory()->for($sender)->create(['skill_type' => 'wanted']);

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($wantedSkill, $receiverSkill))
            ->assertSessionHasErrors('sender_user_skill_id');
    }

    public function test_receiver_skill_must_belong_to_receiver(): void
    {
        [$sender, $receiver, $senderSkill] = $this->exchangePair();
        $other = $this->student('เจ้าของทักษะคนอื่น');
        $otherSkill = $this->offeredSkill($other, 'ทักษะของคนอื่น');

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $otherSkill))
            ->assertSessionHasErrors('receiver_user_skill_id');
    }

    public function test_receiver_skill_must_be_offered(): void
    {
        [$sender, $receiver, $senderSkill] = $this->exchangePair();
        $wantedSkill = UserSkill::factory()->for($receiver)->create(['skill_type' => 'wanted']);

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $wantedSkill))
            ->assertSessionHasErrors('receiver_user_skill_id');
    }

    public function test_inactive_or_deleted_skills_cannot_be_used(): void
    {
        [$sender, $receiver, , $receiverSkill] = $this->exchangePair();
        $inactive = $this->offeredSkill($sender, 'ทักษะปิดใช้');
        $inactive->skill->update(['is_active' => false]);
        $deleted = $this->offeredSkill($sender, 'ทักษะถูกลบ');
        $deleted->skill->delete();

        foreach ([$inactive, $deleted] as $skill) {
            $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($skill, $receiverSkill))
                ->assertSessionHasErrors('sender_user_skill_id');
        }
    }

    public function test_duplicate_pending_request_is_rejected_with_thai_feedback(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        ExchangeRequest::factory()->create($this->requestAttributes($sender, $receiver, $senderSkill, $receiverSkill));

        $this->actingAs($sender)->post(route('exchange-requests.store', $receiver), $this->payload($senderSkill, $receiverSkill))
            ->assertSessionHasErrors(['receiver_user_skill_id' => 'คุณมีคำขอที่รอดำเนินการสำหรับทักษะคู่นี้แล้ว']);
    }

    public function test_reverse_direction_request_is_allowed(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        ExchangeRequest::factory()->create($this->requestAttributes($sender, $receiver, $senderSkill, $receiverSkill));

        $this->actingAs($receiver)->post(route('exchange-requests.store', $sender), $this->payload($receiverSkill, $senderSkill))
            ->assertRedirectToRoute('exchange-requests.index', ['tab' => 'sent']);

        $this->assertDatabaseCount('exchange_requests', 2);
    }

    public function test_student_only_sees_own_requests_and_cannot_view_another_request(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        $request = ExchangeRequest::factory()->create($this->requestAttributes($sender, $receiver, $senderSkill, $receiverSkill));
        $outsider = $this->student('ผู้ใช้ภายนอก');

        $this->actingAs($sender)->get(route('exchange-requests.index', ['tab' => 'sent']))->assertSee($receiver->name);
        $this->actingAs($outsider)->get(route('exchange-requests.index', ['tab' => 'sent']))->assertDontSee($receiver->name);
        $this->actingAs($outsider)->get(route('exchange-requests.show', $request))->assertForbidden();
    }

    public function test_received_pending_request_appears_in_received_tab(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        ExchangeRequest::factory()->create($this->requestAttributes($sender, $receiver, $senderSkill, $receiverSkill));

        $this->actingAs($receiver)->get(route('exchange-requests.index', ['tab' => 'received']))
            ->assertOk()
            ->assertSee($sender->name)
            ->assertSee('ได้รับ');
    }

    public function test_non_pending_request_is_preserved_in_history(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->exchangePair();
        $request = ExchangeRequest::factory()->create($this->requestAttributes($sender, $receiver, $senderSkill, $receiverSkill, ['status' => 'rejected']));

        $this->actingAs($sender)->get(route('exchange-requests.index', ['tab' => 'history']))
            ->assertOk()
            ->assertSee($receiver->name)
            ->assertSee('ประวัติ');
        $this->actingAs($receiver)->get(route('exchange-requests.show', $request))->assertOk();
    }

    /** @return array{User, User, UserSkill, UserSkill} */
    private function exchangePair(): array
    {
        $sender = $this->student('ผู้ส่ง');
        $receiver = $this->student('ผู้รับ');

        return [$sender, $receiver, $this->offeredSkill($sender, 'ทักษะผู้ส่ง'), $this->offeredSkill($receiver, 'ทักษะผู้รับ')];
    }

    private function student(string $name, string $status = 'active'): User
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'student', 'status' => $status]);
        $user->studentProfile()->create();

        return $user;
    }

    private function offeredSkill(User $user, string $name): UserSkill
    {
        return UserSkill::factory()->for($user)->for(Skill::factory()->create(['name' => $name]))->create(['skill_type' => 'offered']);
    }

    private function payload(UserSkill $senderSkill, UserSkill $receiverSkill, array $overrides = []): array
    {
        return array_merge([
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
            'learning_format' => 'online',
            'preferred_schedule' => 'เสาร์ 10:00 น.',
            'message' => 'อยากแลกเปลี่ยนทักษะกัน',
        ], $overrides);
    }

    private function requestAttributes(User $sender, User $receiver, UserSkill $senderSkill, UserSkill $receiverSkill, array $overrides = []): array
    {
        return array_merge([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
            'learning_format' => 'online',
            'preferred_schedule' => 'เสาร์ 10:00 น.',
            'message' => 'อยากแลกเปลี่ยนทักษะกัน',
            'status' => 'pending',
        ], $overrides);
    }
}
