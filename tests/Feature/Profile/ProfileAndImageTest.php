<?php

namespace Tests\Feature\Profile;

use App\Models\StudentProfile;
use App\Models\User;
use App\Services\ProfileImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class ProfileAndImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_own_profile_and_edit_form(): void
    {
        [$user, $profile] = $this->studentWithProfile();
        $profile->update(['faculty' => 'วิศวกรรมศาสตร์']);

        $this->actingAs($user)->get(route('profile.show'))
            ->assertOk()
            ->assertSee('วิศวกรรมศาสตร์');

        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
    }

    public function test_student_can_update_own_profile(): void
    {
        [$user, $profile] = $this->studentWithProfile();

        $this->actingAs($user)->put(route('profile.update'), [
            'faculty' => 'วิทยาศาสตร์',
            'major' => 'วิทยาการคอมพิวเตอร์',
            'year_level' => 3,
            'bio' => 'ชอบแลกเปลี่ยนความรู้',
            'phone' => '0812345678',
            'contact_channel' => 'student@example.test',
        ])->assertRedirectToRoute('profile.show');

        $this->assertDatabaseHas('student_profiles', [
            'id' => $profile->id,
            'faculty' => 'วิทยาศาสตร์',
            'year_level' => 3,
        ]);
    }

    public function test_profile_fields_are_nullable(): void
    {
        [$user, $profile] = $this->studentWithProfile();
        $profile->update(['faculty' => 'วิทยาศาสตร์', 'year_level' => 2]);

        $this->actingAs($user)->put(route('profile.update'), [
            'faculty' => null,
            'major' => null,
            'year_level' => null,
            'bio' => null,
            'phone' => null,
            'contact_channel' => null,
        ])->assertRedirectToRoute('profile.show');

        $this->assertDatabaseHas('student_profiles', ['id' => $profile->id, 'faculty' => null, 'year_level' => null]);
    }

    public function test_year_level_must_be_between_one_and_eight(): void
    {
        [$user] = $this->studentWithProfile();

        $this->from(route('profile.edit'))->actingAs($user)->put(route('profile.update'), ['year_level' => 0])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('year_level');

        $this->from(route('profile.edit'))->actingAs($user)->put(route('profile.update'), ['year_level' => 9])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('year_level');
    }

    public function test_student_cannot_authorize_another_students_profile(): void
    {
        [$student] = $this->studentWithProfile();
        [, $otherProfile] = $this->studentWithProfile();

        $this->actingAs($student)->get(route('profile.show'))->assertOk();
        $this->assertFalse($student->can('update', $otherProfile));
        $this->assertFalse($student->can('delete', $otherProfile));
    }

    public function test_admin_cannot_access_student_profile_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('profile.show'))->assertForbidden();
    }

    public function test_suspended_student_is_redirected_from_profile(): void
    {
        [$user] = $this->studentWithProfile(['status' => 'suspended']);

        $this->actingAs($user)->get(route('profile.show'))->assertRedirectToRoute('account.suspended');
    }

    public function test_image_rejects_disallowed_mime_and_extension(): void
    {
        Storage::fake('public');
        [$user] = $this->studentWithProfile();

        $this->actingAs($user)->post(route('profile.image.store'), [
            'profile_image' => UploadedFile::fake()->create('profile.gif', 20, 'image/gif'),
        ])->assertSessionHasErrors('profile_image');

        $this->actingAs($user)->post(route('profile.image.store'), [
            'profile_image' => UploadedFile::fake()->create('profile.svg', 20, 'image/svg+xml'),
        ])->assertSessionHasErrors('profile_image');
    }

    public function test_image_rejects_extension_that_does_not_match_allowed_extensions(): void
    {
        Storage::fake('public');
        [$user] = $this->studentWithProfile();

        $this->actingAs($user)->post(route('profile.image.store'), [
            'profile_image' => UploadedFile::fake()->image('profile.txt'),
        ])->assertSessionHasErrors('profile_image');
    }

    public function test_image_rejects_files_larger_than_two_megabytes(): void
    {
        Storage::fake('public');
        [$user] = $this->studentWithProfile();

        $this->actingAs($user)->post(route('profile.image.store'), [
            'profile_image' => UploadedFile::fake()->image('profile.jpg')->size(2049),
        ])->assertSessionHasErrors('profile_image');
    }

    public function test_image_upload_uses_a_safe_relative_filename(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->studentWithProfile();

        $this->actingAs($user)->post(route('profile.image.store'), [
            'profile_image' => UploadedFile::fake()->image('../../unsafe name.jpg'),
        ])->assertRedirectToRoute('profile.edit');

        $path = $profile->refresh()->profile_image;
        $this->assertMatchesRegularExpression('#^profile-images/'.$user->id.'/[0-9a-f-]+\\.jpg$#', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_image_upload_compensates_when_database_update_fails(): void
    {
        Storage::fake('public');
        [, $profile] = $this->studentWithProfile();
        $service = new class extends ProfileImageService
        {
            protected function persistImagePath(StudentProfile $profile, string $path): void
            {
                throw new RuntimeException('database failed');
            }
        };

        try {
            $service->store($profile, UploadedFile::fake()->image('profile.jpg'));
            $this->fail('Expected database failure was not thrown.');
        } catch (RuntimeException) {
            $this->assertSame([], Storage::disk('public')->allFiles('profile-images/'.$profile->user_id));
            $this->assertNull($profile->refresh()->profile_image);
        }
    }

    public function test_image_replacement_compensates_when_database_update_fails(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->studentWithProfile();
        $oldPath = 'profile-images/'.$user->id.'/old.jpg';
        $profile->update(['profile_image' => $oldPath]);
        Storage::disk('public')->put($oldPath, 'old');
        $service = new class extends ProfileImageService
        {
            protected function persistImagePath(StudentProfile $profile, string $path): void
            {
                throw new RuntimeException('database failed');
            }
        };

        try {
            $service->store($profile, UploadedFile::fake()->image('new.jpg'));
            $this->fail('Expected database failure was not thrown.');
        } catch (RuntimeException) {
            Storage::disk('public')->assertExists($oldPath);
            $this->assertSame($oldPath, $profile->refresh()->profile_image);
            $this->assertSame([$oldPath], Storage::disk('public')->allFiles('profile-images/'.$user->id));
        }
    }

    public function test_successful_image_replacement_deletes_the_old_file_after_database_update(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->studentWithProfile();
        $oldPath = 'profile-images/'.$user->id.'/old.jpg';
        $profile->update(['profile_image' => $oldPath]);
        Storage::disk('public')->put($oldPath, 'old');

        app(ProfileImageService::class)->store($profile, UploadedFile::fake()->image('new.png'));

        $newPath = $profile->refresh()->profile_image;
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_image_removal_clears_database_and_removes_file(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->studentWithProfile();
        $path = 'profile-images/'.$user->id.'/profile.jpg';
        $profile->update(['profile_image' => $path]);
        Storage::disk('public')->put($path, 'image');

        $this->actingAs($user)->delete(route('profile.image.destroy'))->assertRedirectToRoute('profile.edit');

        $this->assertNull($profile->refresh()->profile_image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_image_removal_clears_database_when_file_is_already_missing(): void
    {
        Storage::fake('public');
        Log::spy();
        [$user, $profile] = $this->studentWithProfile();
        $path = 'profile-images/'.$user->id.'/missing.jpg';
        $profile->update(['profile_image' => $path]);

        app(ProfileImageService::class)->remove($profile);

        $this->assertNull($profile->refresh()->profile_image);
        Log::shouldHaveReceived('info')->once();
    }

    /** @return array{User, StudentProfile} */
    private function studentWithProfile(array $userAttributes = []): array
    {
        $user = User::factory()->create(array_merge(['role' => 'student', 'status' => 'active'], $userAttributes));
        $profile = $user->studentProfile()->create();

        return [$user, $profile];
    }
}
