<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
        $this->get(route('kasir.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_cashier_login_redirects_to_cashier_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->post(route('login'), [
            'email' => $cashier->email,
            'password' => 'password',
        ])->assertRedirect(route('kasir.dashboard'));
    }

    public function test_admin_and_cashier_see_separate_dashboard_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Dashboard Admin')
            ->assertDontSee('Dashboard Kasir');

        $cashier = User::factory()->create(['role' => 'kasir']);
        $this->actingAs($cashier)
            ->get(route('kasir.dashboard'))
            ->assertSee('Dashboard Kasir')
            ->assertDontSee('Dashboard Admin');
    }

    public function test_admin_profile_displays_logout_button_in_topbar(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.profil'))
            ->assertSee(route('logout'), false)
            ->assertSee('class="topbar-actions"', false)
            ->assertSee('Logout')
            ->assertSee($admin->email);
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_change_password_from_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('profile.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHas('status', 'Password berhasil diperbarui.');

        $this->assertTrue(password_verify('new-password-123', $user->fresh()->password));
    }

    public function test_password_change_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('kasir.profil'))
            ->post(route('profile.password.update'), [
                'current_password' => 'incorrect-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('kasir.profil'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(password_verify('password', $user->fresh()->password));
    }
}
