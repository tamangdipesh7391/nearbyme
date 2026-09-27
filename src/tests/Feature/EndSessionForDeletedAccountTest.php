<?php

namespace Tests\Feature;

use App\Models\ProviderTracker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndSessionForDeletedAccountTest extends TestCase
{
    use RefreshDatabase;

    private function account($role)
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.uniqid().'@gmail.com',
            'password' => bcrypt('secret'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    public function test_deleted_provider_is_logged_out_and_sent_to_login_from_panel()
    {
        $provider = $this->account('provider');
        ProviderTracker::create(['provider_id' => $provider->id, 'current_latitude' => '27.7', 'current_longitude' => '85.3', 'is_active' => 1]);
        $provider->delete();

        $this->withSession(['session_provider' => $provider])
            ->get(route('provider.dashboard'))
            ->assertRedirect(route('provider.login'))
            ->assertSessionHas('error')
            ->assertSessionMissing('session_provider');

        $this->assertEquals(0, ProviderTracker::where('provider_id', $provider->id)->value('is_active'));
    }

    public function test_deleted_user_is_logged_out_and_sent_home_from_public_pages()
    {
        $user = $this->account('user');
        $user->delete();

        $this->withSession(['session_user' => $user])
            ->get(route('home.search'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('session_user');
    }

    public function test_deleted_admin_is_sent_to_admin_login()
    {
        $admin = $this->account('admin');
        $admin->delete();

        $this->withSession(['session_admin' => $admin])
            ->get(route('admin.users'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionMissing('session_admin');
    }

    public function test_existing_account_keeps_its_session()
    {
        $admin = $this->account('admin');

        $this->withSession(['session_admin' => $admin])
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSessionHas('session_admin');
    }
}
