<?php

namespace Tests\Feature;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_locked_tables_and_columns_exist(): void
    {
        $tables = [
            'users' => ['id', 'name', 'student_code', 'email', 'password', 'role', 'status', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'],
            'student_profiles' => ['id', 'user_id', 'faculty', 'major', 'year_level', 'bio', 'phone', 'contact_channel', 'profile_image', 'created_at', 'updated_at'],
            'skills' => ['id', 'name', 'category', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            'user_skills' => ['id', 'user_id', 'skill_id', 'skill_type', 'description', 'created_at', 'updated_at'],
            'exchange_requests' => ['id', 'sender_id', 'receiver_id', 'sender_user_skill_id', 'receiver_user_skill_id', 'learning_format', 'preferred_schedule', 'message', 'status', 'responded_at', 'completed_at', 'created_at', 'updated_at'],
            'notifications' => ['id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at', 'created_at', 'updated_at'],
            'jobs' => ['id', 'queue', 'payload', 'attempts', 'reserved_at', 'available_at', 'created_at'],
            'failed_jobs' => ['id', 'uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'],
        ];

        foreach ($tables as $table => $columns) {
            $this->assertTrue(Schema::hasTable($table));
            $this->assertTrue(Schema::hasColumns($table, $columns));
        }
    }

    public function test_locked_model_relationships_are_declared(): void
    {
        $this->assertInstanceOf(HasOne::class, (new User)->studentProfile());
        $this->assertInstanceOf(HasMany::class, (new User)->userSkills());
        $this->assertInstanceOf(HasMany::class, (new User)->sentExchangeRequests());
        $this->assertInstanceOf(HasMany::class, (new User)->receivedExchangeRequests());
        $this->assertInstanceOf(BelongsTo::class, (new StudentProfile)->user());
        $this->assertInstanceOf(HasMany::class, (new Skill)->userSkills());
        $this->assertInstanceOf(BelongsTo::class, (new UserSkill)->user());
        $this->assertInstanceOf(BelongsTo::class, (new UserSkill)->skill());
        $this->assertInstanceOf(BelongsTo::class, (new ExchangeRequest)->sender());
        $this->assertInstanceOf(BelongsTo::class, (new ExchangeRequest)->receiver());
        $this->assertInstanceOf(BelongsTo::class, (new ExchangeRequest)->senderUserSkill());
        $this->assertInstanceOf(BelongsTo::class, (new ExchangeRequest)->receiverUserSkill());
    }

    public function test_factories_persist_records_with_the_locked_relationships(): void
    {
        $sender = User::factory()->create();
        StudentProfile::factory()->for($sender)->create();
        $receiver = User::factory()->create();
        $offeredSkill = UserSkill::factory()->for($sender)->for(Skill::factory())->create(['skill_type' => 'offered']);
        $wantedSkill = UserSkill::factory()->for($receiver)->for(Skill::factory())->create(['skill_type' => 'wanted']);

        $exchangeRequest = ExchangeRequest::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'sender_user_skill_id' => $offeredSkill->id,
            'receiver_user_skill_id' => $wantedSkill->id,
        ]);

        $this->assertTrue($sender->studentProfile()->exists());
        $this->assertTrue($sender->userSkills()->whereKey($offeredSkill)->exists());
        $this->assertTrue($exchangeRequest->sender->is($sender));
        $this->assertTrue($exchangeRequest->receiverUserSkill->is($wantedSkill));
    }
}
