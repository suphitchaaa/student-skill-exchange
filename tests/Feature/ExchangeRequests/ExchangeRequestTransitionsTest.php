<?php

namespace Tests\Feature\ExchangeRequests;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeRequestTransitionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiver_can_accept_pending_request(): void
    {
        [$sender, $receiver, $request] = $this->requestPair();

        $this->actingAs($receiver)->patch(route('exchange-requests.accept', $request))->assertRedirect();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'accepted']);
    }

    public function test_sender_cannot_accept_pending_request(): void
    {
        [$sender, , $request] = $this->requestPair();

        $this->actingAs($sender)->patch(route('exchange-requests.accept', $request))->assertForbidden();
    }

    public function test_third_party_cannot_accept_pending_request(): void
    {
        [, , $request] = $this->requestPair();

        $this->actingAs($this->student('บุคคลที่สาม'))->patch(route('exchange-requests.accept', $request))->assertForbidden();
    }

    public function test_non_pending_request_cannot_be_accepted(): void
    {
        [, $receiver, $request] = $this->requestPair(['status' => 'rejected']);

        $this->actingAs($receiver)->patch(route('exchange-requests.accept', $request))->assertForbidden();
    }

    public function test_receiver_can_reject_pending_request(): void
    {
        [, $receiver, $request] = $this->requestPair();

        $this->actingAs($receiver)->patch(route('exchange-requests.reject', $request))->assertRedirect();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'rejected']);
    }

    public function test_sender_cannot_reject_pending_request(): void
    {
        [$sender, , $request] = $this->requestPair();

        $this->actingAs($sender)->patch(route('exchange-requests.reject', $request))->assertForbidden();
    }

    public function test_third_party_cannot_reject_pending_request(): void
    {
        [, , $request] = $this->requestPair();

        $this->actingAs($this->student('บุคคลที่สาม'))->patch(route('exchange-requests.reject', $request))->assertForbidden();
    }

    public function test_non_pending_request_cannot_be_rejected(): void
    {
        [, $receiver, $request] = $this->requestPair(['status' => 'accepted']);

        $this->actingAs($receiver)->patch(route('exchange-requests.reject', $request))->assertForbidden();
    }

    public function test_sender_can_cancel_pending_request(): void
    {
        [$sender, , $request] = $this->requestPair();

        $this->actingAs($sender)->patch(route('exchange-requests.cancel', $request))->assertRedirect();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'cancelled']);
    }

    public function test_receiver_cannot_cancel_pending_request(): void
    {
        [, $receiver, $request] = $this->requestPair();

        $this->actingAs($receiver)->patch(route('exchange-requests.cancel', $request))->assertForbidden();
    }

    public function test_third_party_cannot_cancel_pending_request(): void
    {
        [, , $request] = $this->requestPair();

        $this->actingAs($this->student('บุคคลที่สาม'))->patch(route('exchange-requests.cancel', $request))->assertForbidden();
    }

    public function test_non_pending_request_cannot_be_cancelled(): void
    {
        [$sender, , $request] = $this->requestPair(['status' => 'completed']);

        $this->actingAs($sender)->patch(route('exchange-requests.cancel', $request))->assertForbidden();
    }

    public function test_sender_can_complete_accepted_request(): void
    {
        [$sender, , $request] = $this->requestPair(['status' => 'accepted']);

        $this->actingAs($sender)->patch(route('exchange-requests.complete', $request))->assertRedirect();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'completed']);
    }

    public function test_receiver_can_complete_accepted_request(): void
    {
        [, $receiver, $request] = $this->requestPair(['status' => 'accepted']);

        $this->actingAs($receiver)->patch(route('exchange-requests.complete', $request))->assertRedirect();
        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'completed']);
    }

    public function test_third_party_cannot_complete_accepted_request(): void
    {
        [, , $request] = $this->requestPair(['status' => 'accepted']);

        $this->actingAs($this->student('บุคคลที่สาม'))->patch(route('exchange-requests.complete', $request))->assertForbidden();
    }

    public function test_non_accepted_request_cannot_be_completed(): void
    {
        [$sender, , $request] = $this->requestPair();

        $this->actingAs($sender)->patch(route('exchange-requests.complete', $request))->assertForbidden();
    }

    public function test_transition_timestamps_are_recorded(): void
    {
        [, $receiver, $accepted] = $this->requestPair();
        $this->actingAs($receiver)->patch(route('exchange-requests.accept', $accepted));
        $this->assertNotNull($accepted->refresh()->responded_at);

        [, $rejectingReceiver, $rejected] = $this->requestPair();
        $this->actingAs($rejectingReceiver)->patch(route('exchange-requests.reject', $rejected));
        $this->assertNotNull($rejected->refresh()->responded_at);

        [$sender, , $completed] = $this->requestPair(['status' => 'accepted']);
        $this->actingAs($sender)->patch(route('exchange-requests.complete', $completed));
        $this->assertNotNull($completed->refresh()->completed_at);
    }

    public function test_second_transition_is_rejected_and_record_is_preserved(): void
    {
        [, $receiver, $request] = $this->requestPair();
        $this->actingAs($receiver)->patch(route('exchange-requests.accept', $request))->assertRedirect();
        $this->actingAs($receiver)->patch(route('exchange-requests.accept', $request))->assertForbidden();

        $this->assertDatabaseHas('exchange_requests', ['id' => $request->id, 'status' => 'accepted']);
    }

    /** @return array{User, User, ExchangeRequest} */
    private function requestPair(array $overrides = []): array
    {
        $sender = $this->student('ผู้ส่ง');
        $receiver = $this->student('ผู้รับ');
        $senderSkill = $this->offeredSkill($sender, 'ทักษะผู้ส่ง');
        $receiverSkill = $this->offeredSkill($receiver, 'ทักษะผู้รับ');
        $request = ExchangeRequest::factory()->create(array_merge([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
            'status' => 'pending',
        ], $overrides));

        return [$sender, $receiver, $request];
    }

    private function student(string $name): User
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'student', 'status' => 'active']);
        $user->studentProfile()->create();

        return $user;
    }

    private function offeredSkill(User $user, string $name): UserSkill
    {
        return UserSkill::factory()->for($user)->for(Skill::factory()->create(['name' => $name]))->create(['skill_type' => 'offered']);
    }
}
