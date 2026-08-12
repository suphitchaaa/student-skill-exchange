<?php

namespace Tests\Feature\Auth;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_registration_forms(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_registration_creates_an_active_student_and_empty_profile(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'นักศึกษาทดสอบ',
            'student_code' => '65000001',
            'email' => 'student@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'status' => 'suspended',
        ]);

        $user = User::query()->where('email', 'student@example.test')->firstOrFail();

        $response->assertRedirectToRoute('student.dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertSame('student', $user->role);
        $this->assertSame('active', $user->status);
        $this->assertNull($user->email_verified_at);
        $this->assertDatabaseHas('student_profiles', ['user_id' => $user->id]);
        $this->assertSame(1, StudentProfile::query()->where('user_id', $user->id)->count());
    }

    public function test_active_student_can_log_in_and_reaches_student_dashboard(): void
    {
        $user = $this->userWithPassword('student');

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirectToRoute('student.dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_active_admin_can_log_in_and_reaches_admin_dashboard(): void
    {
        $user = $this->userWithPassword('admin');

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirectToRoute('admin.dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_is_redirected_from_student_and_admin_dashboards(): void
    {
        $this->get(route('student.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_suspended_account_reaches_status_page_without_redirect_loop(): void
    {
        $user = $this->userWithPassword('student', 'suspended');

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirectToRoute('account.suspended');
        $this->get(route('account.suspended'))->assertOk()->assertSee('บัญชีของคุณถูกระงับการใช้งาน');
        $this->get(route('student.dashboard'))->assertRedirectToRoute('account.suspended');
    }

    public function test_suspended_account_can_log_out_from_status_page(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $this->actingAs($user)->post(route('logout'))->assertRedirectToRoute('home');

        $this->assertGuest();
    }

    public function test_logout_is_post_only_and_dashboard_contains_csrf_protected_logout_form(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get(route('logout'))->assertMethodNotAllowed();
        $this->actingAs($user)->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('name="_token"', false);
    }

    private function userWithPassword(string $role, string $status = 'active'): User
    {
        return User::factory()->create([
            'role' => $role,
            'status' => $status,
            'password' => Hash::make('password'),
        ]);
    }
}
