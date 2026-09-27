<?php

namespace Tests\Feature;

use App\Models\RequestedService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteAccountWithActiveBookingsTest extends TestCase
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

    private function booking(User $user, User $provider, $status)
    {
        return RequestedService::create(['user_id' => $user->id, 'provider_id' => $provider->id, 'status' => $status]);
    }

    private function asAdmin()
    {
        return $this->withSession(['session_admin' => $this->account('admin')]);
    }

    public function test_provider_with_pending_and_in_progress_bookings_cannot_be_deleted()
    {
        $user = $this->account('user');
        $provider = $this->account('provider');
        $this->booking($user, $provider, 'pending');
        $this->booking($user, $provider, 'confirmed');

        $this->asAdmin()->get(route('admin.providers.soft_delete', $provider->id))
            ->assertRedirect(route('admin.providers'))
            ->assertSessionHas('error', 'Cannot delete this provider: they have 1 pending and 1 in-progress bookings. Resolve them before deleting.');

        $this->assertNotSoftDeleted($provider);
    }

    public function test_user_with_pending_booking_cannot_be_deleted()
    {
        $user = $this->account('user');
        $this->booking($user, $this->account('provider'), 'pending');

        $this->asAdmin()->get(route('admin.users.soft_delete', $user->id))
            ->assertSessionHas('error', 'Cannot delete this user: they have 1 pending booking. Resolve them before deleting.');

        $this->assertNotSoftDeleted($user);
    }

    public function test_trashed_user_with_active_booking_cannot_be_permanently_deleted()
    {
        $user = $this->account('user');
        $this->booking($user, $this->account('provider'), 'confirmed');
        $user->delete();

        $this->asAdmin()->get(route('admin.users.delete', $user->id))->assertSessionHas('error');

        $this->assertSoftDeleted($user);
    }

    public function test_accounts_with_only_resolved_bookings_can_be_deleted()
    {
        $user = $this->account('user');
        $provider = $this->account('provider');
        foreach (['completed', 'rejected', 'cancelled'] as $status) {
            $this->booking($user, $provider, $status);
        }

        $this->asAdmin()->get(route('admin.users.soft_delete', $user->id))->assertSessionHas('success');
        $this->asAdmin()->get(route('admin.providers.soft_delete', $provider->id))->assertSessionHas('success');

        $this->assertSoftDeleted($user);
        $this->assertSoftDeleted($provider);
    }

    public function test_blocked_delete_returns_to_the_tab_it_came_from()
    {
        $user = $this->account('user');
        $provider = $this->account('provider');
        $this->booking($user, $provider, 'pending');

        $this->asAdmin()->get(route('admin.providers.soft_delete', [$provider->id, 'tab' => 'active']))
            ->assertRedirect(route('admin.providers'))
            ->assertSessionHas('tab', 'active')
            ->assertSessionHas('error');

        $this->get(route('admin.providers'))
            ->assertSee('class="nav-link active" id="nav-home-tab"', false)
            ->assertSee('class="tab-pane fade show active" id="nav-active"', false);
    }
}
