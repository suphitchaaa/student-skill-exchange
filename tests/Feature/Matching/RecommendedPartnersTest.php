<?php

namespace Tests\Feature\Matching;

use App\Models\ExchangeRequest;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use App\Queries\StudentMatchQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecommendedPartnersTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_way_candidate_is_recommended(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');

        $results = $this->recommendationsFor($current);

        $this->assertSame([$candidate->id], $results->pluck('id')->all());
        $this->assertFalse((bool) $results->first()->is_mutual_match);
    }

    public function test_mutual_candidate_is_recommended(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $wanted = $this->skill('Microsoft Excel');
        $offered = $this->skill('Canva');

        $this->attachSkill($current, $wanted, 'wanted');
        $this->attachSkill($current, $offered, 'offered');
        $this->attachSkill($candidate, $wanted, 'offered');
        $this->attachSkill($candidate, $offered, 'wanted');

        $result = $this->recommendationsFor($current)->sole();

        $this->assertSame($candidate->id, $result->id);
        $this->assertTrue((bool) $result->is_mutual_match);
    }

    public function test_mutual_candidate_is_ordered_before_one_way_candidate(): void
    {
        $current = $this->student('Current Student');
        $oneWay = $this->student('A One Way');
        $mutual = $this->student('Z Mutual');
        $wanted = $this->skill('Microsoft Excel');
        $offered = $this->skill('Canva');

        $this->attachSkill($current, $wanted, 'wanted');
        $this->attachSkill($current, $offered, 'offered');

        $this->attachSkill($oneWay, $wanted, 'offered');

        $this->attachSkill($mutual, $wanted, 'offered');
        $this->attachSkill($mutual, $offered, 'wanted');

        $this->assertSame(
            [$mutual->id, $oneWay->id],
            $this->recommendationsFor($current)->pluck('id')->all()
        );
    }

    public function test_candidates_are_stably_ordered_by_name_then_id_within_tier(): void
    {
        $current = $this->student('Current Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');

        $aliceFirst = $this->student('Alice');
        $aliceSecond = $this->student('Alice');
        $charlie = $this->student('Charlie');

        $this->attachSkill($charlie, $skill, 'offered');
        $this->attachSkill($aliceFirst, $skill, 'offered');
        $this->attachSkill($aliceSecond, $skill, 'offered');

        $this->assertSame(
            [$aliceFirst->id, $aliceSecond->id, $charlie->id],
            $this->recommendationsFor($current)->pluck('id')->all()
        );
    }

    public function test_current_user_is_never_recommended(): void
    {
        $current = $this->student('Current Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($current, $skill, 'offered');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_user_without_active_wanted_skills_gets_no_recommendations(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'offered');
        $this->attachSkill($candidate, $skill, 'wanted');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_admin_is_excluded(): void
    {
        $current = $this->student('Current Student');
        $admin = User::factory()->create([
            'name' => 'Admin Candidate',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($admin, $skill, 'offered');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_suspended_student_is_excluded(): void
    {
        $current = $this->student('Current Student');
        $candidate = User::factory()->create([
            'name' => 'Suspended Candidate',
            'role' => 'student',
            'status' => 'suspended',
        ]);
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_inactive_skill_does_not_create_a_match(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Inactive Skill', false);

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_soft_deleted_skill_does_not_create_a_match(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Retired Skill');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');
        $skill->delete();

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_current_wanted_must_match_candidate_offered(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'wanted');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_reverse_only_match_does_not_recommend_candidate(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Canva');

        $this->attachSkill($current, $skill, 'offered');
        $this->attachSkill($candidate, $skill, 'wanted');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_thai_skill_identity_matches_normally(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('การออกแบบกราฟิก');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');

        $this->assertSame(
            [$candidate->id],
            $this->recommendationsFor($current)->pluck('id')->all()
        );
    }

    public function test_matching_uses_skill_id_not_similar_display_name(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');

        $currentSkill = $this->skill('Canva');
        $candidateSkill = $this->skill('Canva Design');

        $this->assertNotSame($currentSkill->id, $candidateSkill->id);

        $this->attachSkill($current, $currentSkill, 'wanted');
        $this->attachSkill($candidate, $candidateSkill, 'offered');

        $this->assertTrue($this->recommendationsFor($current)->isEmpty());
    }

    public function test_dashboard_recommendations_are_limited_to_three_and_use_locked_order(): void
    {
        $current = $this->student('Current Student');
        $wanted = $this->skill('Microsoft Excel');
        $offered = $this->skill('Canva');

        $this->attachSkill($current, $wanted, 'wanted');
        $this->attachSkill($current, $offered, 'offered');

        $oneWayA = $this->student('A One Way');
        $mutualZ = $this->student('Z Mutual');
        $oneWayB = $this->student('B One Way');
        $oneWayC = $this->student('C One Way');

        foreach ([$oneWayA, $mutualZ, $oneWayB, $oneWayC] as $candidate) {
            $this->attachSkill($candidate, $wanted, 'offered');
        }

        $this->attachSkill($mutualZ, $offered, 'wanted');

        $results = (new StudentMatchQuery)->dashboardRecommendationsFor($current, 3);

        $this->assertCount(3, $results);
        $this->assertSame(
            [$mutualZ->id, $oneWayA->id, $oneWayB->id],
            $results->pluck('id')->all()
        );
        $this->assertTrue($results->every(fn (User $user): bool => $user->relationLoaded('userSkills')));
    }

    public function test_dashboard_shows_recommendations_and_profile_links(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($candidate, $skill, 'offered');

        $response = $this
            ->actingAs($current)
            ->get(route('student.dashboard'));

        $response
            ->assertOk()
            ->assertSee('คนที่อาจเหมาะกับคุณ')
            ->assertSee('Candidate Student')
            ->assertSee('Microsoft Excel')
            ->assertSee('มีทักษะที่คุณกำลังมองหา')
            ->assertSee(route('students.show', $candidate), false);
    }

    public function test_dashboard_shows_compact_empty_state_when_no_match(): void
    {
        $current = $this->student('Current Student');

        $response = $this
            ->actingAs($current)
            ->get(route('student.dashboard'));

        $response
            ->assertOk()
            ->assertSee('ยังไม่มีคนที่ตรงกับทักษะที่คุณกำลังมองหา')
            ->assertSee('จัดการทักษะที่ต้องการเรียน')
            ->assertSee(route('user-skills.index', ['type' => 'wanted']), false);
    }

    public function test_recommended_filter_only_shows_recommended_candidates(): void
    {
        $current = $this->student('Current Student');
        $matched = $this->student('Matched Student');
        $unmatched = $this->student('Unmatched Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($matched, $skill, 'offered');

        $response = $this
            ->actingAs($current)
            ->get(route('students.index', ['recommended' => 1]));

        $response
            ->assertOk()
            ->assertSee('แนะนำสำหรับฉัน')
            ->assertSee('Matched Student')
            ->assertDontSee('Unmatched Student');
    }

    public function test_recommended_filter_combines_with_existing_filters_using_and(): void
    {
        $current = $this->student('Current Student');
        $matchingFaculty = $this->student('Matching Faculty');
        $otherFaculty = $this->student('Other Faculty');
        $skill = $this->skill('Microsoft Excel');

        $current->studentProfile()->create();
        $matchingFaculty->studentProfile()->create([
            'faculty' => 'วิศวกรรมศาสตร์',
            'year_level' => 3,
        ]);
        $otherFaculty->studentProfile()->create([
            'faculty' => 'บริหารธุรกิจ',
            'year_level' => 3,
        ]);

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($matchingFaculty, $skill, 'offered');
        $this->attachSkill($otherFaculty, $skill, 'offered');

        $response = $this
            ->actingAs($current)
            ->get(route('students.index', [
                'recommended' => 1,
                'faculty' => 'วิศวกรรมศาสตร์',
            ]));

        $response
            ->assertOk()
            ->assertSee('Matching Faculty')
            ->assertDontSee('Other Faculty');
    }

    public function test_recommended_parameter_is_retained_in_pagination_links(): void
    {
        $current = $this->student('Current Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');

        foreach (range(1, 10) as $number) {
            $candidate = $this->student(sprintf('Candidate %02d', $number));
            $this->attachSkill($candidate, $skill, 'offered');
        }

        $response = $this
            ->actingAs($current)
            ->get(route('students.index', ['recommended' => 1]));

        $response
            ->assertOk()
            ->assertSee('recommended=1', false);
    }

    public function test_search_behavior_is_unchanged_without_recommended_filter(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Reverse Only Student');
        $skill = $this->skill('Canva');

        $this->attachSkill($current, $skill, 'offered');
        $this->attachSkill($candidate, $skill, 'wanted');

        $response = $this
            ->actingAs($current)
            ->get(route('students.index'));

        $response
            ->assertOk()
            ->assertSee('Reverse Only Student');
    }

    public function test_profile_marks_offered_skill_matching_current_wanted_skill(): void
    {
        $current = $this->student('Current Student');
        $viewed = $this->student('Viewed Student');
        $skill = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $skill, 'wanted');
        $this->attachSkill($viewed, $skill, 'offered');

        $response = $this
            ->actingAs($current)
            ->get(route('students.show', $viewed));

        $response
            ->assertOk()
            ->assertSee('Microsoft Excel')
            ->assertSee('ทักษะนี้ตรงกับสิ่งที่คุณกำลังมองหา');
    }

    public function test_profile_marks_wanted_skill_matching_current_offered_skill(): void
    {
        $current = $this->student('Current Student');
        $viewed = $this->student('Viewed Student');
        $skill = $this->skill('Canva');

        $this->attachSkill($current, $skill, 'offered');
        $this->attachSkill($viewed, $skill, 'wanted');

        $response = $this
            ->actingAs($current)
            ->get(route('students.show', $viewed));

        $response
            ->assertOk()
            ->assertSee('Canva')
            ->assertSee('เขากำลังมองหาทักษะที่คุณมี');
    }

    public function test_profile_does_not_mark_unmatched_skills(): void
    {
        $current = $this->student('Current Student');
        $viewed = $this->student('Viewed Student');
        $currentWanted = $this->skill('Microsoft Excel');
        $viewedOffered = $this->skill('Canva Design');

        $this->attachSkill($current, $currentWanted, 'wanted');
        $this->attachSkill($viewed, $viewedOffered, 'offered');

        $response = $this
            ->actingAs($current)
            ->get(route('students.show', $viewed));

        $response
            ->assertOk()
            ->assertSee('Canva Design')
            ->assertDontSee('ทักษะนี้ตรงกับสิ่งที่คุณกำลังมองหา')
            ->assertDontSee('เขากำลังมองหาทักษะที่คุณมี');
    }

    public function test_own_profile_has_no_contextual_match_labels(): void
    {
        $current = $this->student('Current Student');
        $offered = $this->skill('Canva');
        $wanted = $this->skill('Microsoft Excel');

        $this->attachSkill($current, $offered, 'offered');
        $this->attachSkill($current, $wanted, 'wanted');

        $response = $this
            ->actingAs($current)
            ->get(route('students.show', $current));

        $response
            ->assertOk()
            ->assertSee('Canva')
            ->assertSee('Microsoft Excel')
            ->assertDontSee('ทักษะนี้ตรงกับสิ่งที่คุณกำลังมองหา')
            ->assertDontSee('เขากำลังมองหาทักษะที่คุณมี');
    }

    public function test_candidate_remains_recommended_when_an_existing_request_allows_another_skill_pair(): void
    {
        $current = $this->student('Current Student');
        $candidate = $this->student('Candidate Student');

        $recommendationSkill = $this->skill('Microsoft Excel');
        $currentRequestSkill = $this->skill('Canva');
        $candidateRequestSkill = $this->skill('Presentation');

        $this->attachSkill($current, $recommendationSkill, 'wanted');
        $this->attachSkill($candidate, $recommendationSkill, 'offered');

        $currentOffered = $this->attachSkill($current, $currentRequestSkill, 'offered');
        $candidateOffered = $this->attachSkill($candidate, $candidateRequestSkill, 'offered');

        ExchangeRequest::factory()->create([
            'sender_id' => $current->id,
            'receiver_id' => $candidate->id,
            'sender_user_skill_id' => $currentOffered->id,
            'receiver_user_skill_id' => $candidateOffered->id,
            'learning_format' => 'online',
            'preferred_schedule' => 'เสาร์ 10:00 น.',
            'message' => 'คำขอเดิมคนละคู่ทักษะ',
            'status' => 'pending',
        ]);

        $results = $this->recommendationsFor($current);

        $this->assertSame([$candidate->id], $results->pluck('id')->all());

        $this
            ->actingAs($current)
            ->get(route('students.index', ['recommended' => 1]))
            ->assertOk()
            ->assertSee('Candidate Student');
    }

    public function test_matching_uses_existing_database_contract_only(): void
    {
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'student_code',
            'email',
            'email_verified_at',
            'password',
            'role',
            'status',
            'remember_token',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('users'));

        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'normalized_name',
            'category',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ], Schema::getColumnListing('skills'));

        $this->assertEqualsCanonicalizing([
            'id',
            'user_id',
            'skill_id',
            'skill_type',
            'description',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('user_skills'));

        $this->assertEqualsCanonicalizing([
            'id',
            'sender_id',
            'receiver_id',
            'sender_user_skill_id',
            'receiver_user_skill_id',
            'learning_format',
            'preferred_schedule',
            'message',
            'status',
            'responded_at',
            'completed_at',
            'created_at',
            'updated_at',
        ], Schema::getColumnListing('exchange_requests'));

        $this->assertFalse(Schema::hasTable('matches'));
        $this->assertFalse(Schema::hasTable('recommendations'));
    }

    private function recommendationsFor(User $current): Collection
    {
        $query = new StudentMatchQuery;

        return $query
            ->applyRecommendation($query->eligibleStudentsFor($current), $current)
            ->get();
    }

    private function student(string $name): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function skill(string $name, bool $isActive = true): Skill
    {
        return Skill::query()->create([
            'name' => $name,
            'category' => 'ทั่วไป',
            'is_active' => $isActive,
        ]);
    }

    private function attachSkill(User $user, Skill $skill, string $skillType): UserSkill
    {
        return UserSkill::query()->create([
            'user_id' => $user->id,
            'skill_id' => $skill->id,
            'skill_type' => $skillType,
            'description' => null,
        ]);
    }
}
