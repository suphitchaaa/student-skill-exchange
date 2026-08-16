<?php

namespace Tests\Feature\Notifications;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use App\Notifications\ExchangeRequestNotification;
use App\Services\ExchangeRequestService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExchangeRequestObserverTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        config(['queue.default' => 'database']);
    }

    public function test_created_request_queues_notification_for_receiver(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->requestPair(false);
        ExchangeRequest::factory()->create($this->attributes($sender, $receiver, $senderSkill, $receiverSkill));

        $this->assertSame(2, DB::table('jobs')->count());
        $this->assertSame(0, DB::table('notifications')->count());
    }

    public function test_accepted_request_queues_notification_for_sender(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        app(ExchangeRequestService::class)->accept($receiver, $request);

        $this->assertSame(2, DB::table('jobs')->count());
    }

    public function test_rejected_request_queues_notification_for_sender(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        app(ExchangeRequestService::class)->reject($receiver, $request);

        $this->assertSame(2, DB::table('jobs')->count());
    }

    public function test_cancelled_request_queues_notification_for_receiver(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        app(ExchangeRequestService::class)->cancel($sender, $request);

        $this->assertSame(2, DB::table('jobs')->count());
    }

    public function test_completed_request_queues_two_notifications(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver, ['status' => 'accepted']);

        app(ExchangeRequestService::class)->complete($sender, $request);

        $this->assertSame(4, DB::table('jobs')->count());
    }

    public function test_non_status_update_does_not_queue_notification(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        $request->update(['preferred_schedule' => 'อาทิตย์ 13:00 น.']);

        $this->assertSame(0, DB::table('jobs')->count());
    }

    public function test_saving_the_same_status_does_not_queue_notification(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        $request->update(['status' => 'pending']);

        $this->assertSame(0, DB::table('jobs')->count());
    }

    public function test_rolled_back_transaction_does_not_queue_notification(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->requestPair();

        DB::beginTransaction();
        try {
            ExchangeRequest::factory()->create($this->attributes($sender, $receiver, $senderSkill, $receiverSkill));
            throw new \RuntimeException('rollback test');
        } catch (\RuntimeException) {
            DB::rollBack();
        }

        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertSame(0, DB::table('exchange_requests')->count());
    }

    public function test_transition_notification_is_queued_only_after_commit(): void
    {
        [$sender, $receiver] = $this->requestPair();
        $request = $this->requestFor($sender, $receiver);

        DB::beginTransaction();
        app(ExchangeRequestService::class)->accept($receiver, $request);
        $this->assertSame(0, DB::table('jobs')->count());
        DB::commit();

        $this->assertSame(2, DB::table('jobs')->count());
    }

    public function test_notification_uses_database_and_mail_with_minimal_payload(): void
    {
        $notification = new ExchangeRequestNotification(42, 'completed');

        $this->assertSame(['database', 'mail'], $notification->via($this->student('ผู้รับ')));
        $this->assertSame([
            'exchange_request_id' => 42,
            'event' => 'completed',
            'message' => 'กิจกรรมแลกเปลี่ยนทักษะเสร็จสิ้นแล้ว',
        ], $notification->toDatabase($this->student('ผู้รับ')));
    }

    public function test_queued_notification_payload_references_request_without_serializing_model(): void
    {
        [$sender, $receiver, $senderSkill, $receiverSkill] = $this->requestPair(false);
        $request = ExchangeRequest::factory()->create($this->attributes($sender, $receiver, $senderSkill, $receiverSkill));
        $job = DB::table('jobs')->first();

        $this->assertNotNull($job);
        $this->assertStringContainsString('ExchangeRequestNotification', $job->payload);
        $this->assertStringContainsString((string) $request->id, $job->payload);
    }

    /** @return array{User, User, UserSkill, UserSkill} */
    private function requestPair(bool $clearJobs = true): array
    {
        $sender = $this->student('ผู้ส่ง');
        $receiver = $this->student('ผู้รับ');
        $senderSkill = $this->offeredSkill($sender, 'ทักษะผู้ส่ง');
        $receiverSkill = $this->offeredSkill($receiver, 'ทักษะผู้รับ');

        if ($clearJobs) {
            DB::table('jobs')->delete();
        }

        return [$sender, $receiver, $senderSkill, $receiverSkill];
    }

    private function requestFor(User $sender, User $receiver, array $overrides = []): ExchangeRequest
    {
        $senderSkill = $sender->userSkills()->where('skill_type', 'offered')->firstOrFail();
        $receiverSkill = $receiver->userSkills()->where('skill_type', 'offered')->firstOrFail();

        $request = ExchangeRequest::factory()->create($this->attributes($sender, $receiver, $senderSkill, $receiverSkill, $overrides));
        DB::table('jobs')->delete();

        return $request;
    }

    private function attributes(User $sender, User $receiver, UserSkill $senderSkill, UserSkill $receiverSkill, array $overrides = []): array
    {
        return array_merge([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $senderSkill->id,
            'receiver_user_skill_id' => $receiverSkill->id,
            'learning_format' => 'online',
            'preferred_schedule' => 'เสาร์ 10:00 น.',
            'message' => 'ทดสอบการแจ้งเตือน',
            'status' => 'pending',
        ], $overrides);
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
